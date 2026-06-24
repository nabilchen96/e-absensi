<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Auth;
use DB;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index()
    {
        $data = DB::table('users')
                ->leftjoin('detail_users', 'detail_users.user_id', '=', 'users.id')
                ->leftjoin('lokasi_kerjas', 'lokasi_kerjas.id', '=', 'detail_users.satuan_kerja')
                ->where('users.id', Auth::id())
                ->select(
                    'lokasi_kerjas.latitude',
                    'lokasi_kerjas.longitude'
                )
                ->first();

        return view('backend.absensi.index', [
            'data' => $data
        ]);
    }

    public function getData(Request $request)
    {
        $keyword = $request->keyword;
        $shift = $request->shift;
        $status_absensi = $request->status_absensi;

        $query = DB::table('absensis as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->leftjoin('detail_users', 'detail_users.user_id', '=', 'u.id')
            ->where('u.role', 'Pegawai')
            ->orderBy('a.id', 'DESC');

        // 🔍 keyword
        if ($keyword) {
            $query->where('u.name', 'like', "%$keyword%");
        }

        // filter shift
        if ($shift && $shift != 'Semua') {
            $query->where('a.status_shift', $shift);
        }

        // filter status
        if ($status_absensi && $status_absensi != 'Semua') {
            $query->where('a.status_absensi', $status_absensi);
        }

        // 👑 ADMIN → semua data
        if (Auth::user()->role == 'Admin') {

            $data = $query->select('a.*', 'u.name', 'detail_users.nip')->get();

        }
        // 🏢 OPD → pegawai satu unit kerja
        elseif (Auth::user()->role == 'OPD') {

            $idUnitKerja = Auth::user()->id_unit_kerja_pandu;

            $data = $query
                ->join('lokasi_kerja_users as lku', 'lku.id_user', '=', 'u.id')
                ->join('lokasi_kerjas as lk', 'lk.id', '=', 'lku.id_lokasi_kerja')
                ->where('lk.id_pandu', $idUnitKerja)
                ->select(
                    'a.*', 
                    'u.name', 
                    'lk.lokasi_kerja',
                    'detail_users.nip'
                )
                ->get();

        }
        // 👤 PEGAWAI → data sendiri
        else {

            $data = $query
                ->where('a.user_id', Auth::id())
                ->select('a.*', 'u.name', 'detail_users.nip')
                ->get();
        }

        return response()->json(['data' => $data]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto'      => 'required',
            'latitude'  => 'required',
            'longitude' => 'required',
            'jarak'     => 'required',
            'jenis_absensi' => 'required|in:Masuk,Pulang',
        ],[
            'foto.required'     => 'Klik Ambil Foto Terlebih Dahulu',
            'jenis_absensi.required' => 'Jenis absensi wajib dipilih',
            'jarak.required'    => 'Jarak Wajib Diisi, Pastikan Lokasi Kerja Memiliki Koordinat'
        ]);

        if ($validator->fails()) {

            $data = [
                'responCode' => 0,
                'respon' => $validator->errors()
            ];

        }else{

            // simpan foto (base64) ke public/absensi
            $image = $request->foto;

            // hapus header base64
            $image = explode(',', $image)[1];

            // decode base64
            $imageData = base64_decode($image);

            // nama file
            $imageName = time() . '.png';

            // path ke folder public
            $path = public_path('absensi');

            // buat folder jika belum ada
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // simpan file
            file_put_contents($path . '/' . $imageName, $imageData);


            //SIMPAN BUKTI FILE JIKA ADA
            $buktiFile = null;

            if ($request->hasFile('bukti')) {

                $buktiFile = time().'_'.$request->file('bukti')->getClientOriginalName();

                $request->file('bukti')->move(
                    public_path('bukti-absensi'),
                    $buktiFile
                );
            }

            $jam = now()->format('H:i:s');

            //MENDEFINISIKAN STATUS SHIFT
            if ($request->jenis_absensi == 'Masuk') {

                if ($jam >= '07:00:00' && $jam <= '12:00:00') {
                    $statusShift = 'Reguler';
                }

                elseif ($jam >= '15:00:00' && $jam <= '17:00:00') {
                    $statusShift = 'Sore';
                }

                else {
                    $statusShift = 'Malam';
                }
            }

            // dd($statusShift);

            if ($request->jenis_absensi == 'Pulang') {

                if ($jam >= '16:00:00' && $jam <= '18:00:00') {
                    $statusShift = 'Reguler';
                }

                elseif (
                    $jam >= '23:00:00' ||
                    $jam <= '01:00:00'
                ) {
                    $statusShift = 'Sore';
                }

                elseif (
                    $jam >= '07:00:00' &&
                    $jam <= '08:00:00'
                ) {
                    $statusShift = 'Malam';
                }
            }



            Absensi::create([
                'foto'          => $imageName,
                'latitude'      => $request->latitude,
                'longitude'     => $request->longitude,
                'user_id'       => auth()->user()->id ?? 1,
                'datetime'      => now(),
                'jarak'         => $request->jarak,
                'jenis_absensi' => $request->jenis_absensi,
                'status_shift'  => $statusShift,
                'alasan'        => $request->alasan,
                'bukti'         => $buktiFile,
            ]);


            $data = [
                'responCode' => 1,
                'respon' => 'Data Sukses Disimpan'
            ];
        }


        return response()->json($data);
    }

    public function delete(Request $request)
    {
        Absensi::find($request->id)->delete();

        return response()->json([
            'responCode' => 1,
            'respon' => 'Absensi berhasil dihapus'
        ]);
    }

    public function hitungJarak($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Radius bumi dalam KM

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * asin(sqrt($a));

        return $earthRadius * $c; // hasil dalam KM
    }

    public function storeAbsenTanpaLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto'     => 'required',
            'latitude' => 'required',
            'longitude'=> 'required',
            'email'    => 'required',
            'password' => 'required'
        ],[
            'foto.required' => 'Klik Ambil Foto Terlebih Dahulu'
        ]);

        // dd($request->all());

        if ($validator->fails()) {

            $data = [
                'responCode' => 0,
                'respon' => $validator->errors()
            ];

        }else{

            /* ===============================
            | CEK USER BERDASARKAN EMAIL
            =============================== */
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'responCode' => 0,
                    'respon' => [
                        'email' => [
                            'Email tidak terdaftar atau password salah'
                        ]
                    ]
                ]);
            }

            /* ===============================
            | CEK PASSWORD
            =============================== */
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'responCode' => 0,
                    'respon' => [
                        'email' => [
                            'Email tidak terdaftar atau password salah'
                        ]
                    ]
                ]);
            }

            // simpan foto (base64) ke public/absensi
            $image = $request->foto;

            // hapus header base64
            $image = explode(',', $image)[1];

            // decode base64
            $imageData = base64_decode($image);

            // nama file
            $imageName = time() . '.png';

            // path ke folder public
            $path = public_path('absensi');

            // buat folder jika belum ada
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // simpan file
            file_put_contents($path . '/' . $imageName, $imageData);


            $data = DB::table('users')
                ->leftjoin('detail_users', 'detail_users.user_id', '=', 'users.id')
                ->leftjoin('lokasi_kerjas', 'lokasi_kerjas.id', '=', 'detail_users.satuan_kerja')
                ->where('users.id', $user->id)
                ->select(
                    'lokasi_kerjas.latitude',
                    'lokasi_kerjas.longitude'
                )
                ->first();

            $jarakKm = $this->hitungJarak($data->latitude, $data->longitude, $request->latitude, $request->longitude);
    
            Absensi::Create(
                [
                    'foto'      => $imageName,
                    'latitude'  => $request->latitude,
                    'longitude' => $request->longitude,
                    'user_id'   => $user->id,
                    'datetime'  => now(),
                    'jarak'     => number_format($jarakKm, 3)
                ]
            );

            $data = [
                'responCode' => 1,
                'respon' => 'Data Sukses Direkam'
            ];
        }


        return response()->json($data);
    }

    public function verifikasi(Request $request)
    {
        $absensi = Absensi::find($request->id);

        if (!$absensi) {
            return response()->json([
                'responCode' => 0,
                'respon' => 'Data absensi tidak ditemukan'
            ]);
        }

        if (empty($absensi->status_absensi)) {
            $absensi->status_absensi = 'Diterima';
        } else {
            $absensi->status_absensi =
                $absensi->status_absensi == 'Diterima'
                    ? 'Ditolak'
                    : 'Diterima';
        }

        $absensi->save();

        return response()->json([
            'responCode' => 1,
            'respon' => 'Status absensi berhasil diubah',
            'status_absensi' => $absensi->status_absensi
        ]);
    }
}

