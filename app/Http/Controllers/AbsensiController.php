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

        $data = Absensi::with('user')
            ->whereHas('user', function($q) use ($keyword){
                if ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                }
            })
            ->orderBy('id', 'DESC');

        if(Auth::user()->role == 'Admin'){
            $data = $data->get();
        }else{
            $data = $data->where('user_id', Auth::id())->get();
        }

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto'     => 'required',
            'latitude' => 'required',
            'longitude'=> 'required',
        ],[
            'foto.required' => 'Klik Ambil Foto Terlebih Dahulu'
        ]);

        if ($validator->fails()) {

            $data = [
                'responCode' => 0,
                'respon' => $validator->errors()
            ];

        }else{

            // simpan foto (base64)
            $image = $request->foto;
            $imageName = time().".png";
            Storage::put("public/absensi/".$imageName, base64_decode(explode(",", $image)[1]));
    
            Absensi::Create(
                [
                    'foto'      => $imageName,
                    'latitude'  => $request->latitude,
                    'longitude' => $request->longitude,
                    'user_id'   => auth()->user()->id ?? 1,
                    'datetime'  => now(),
                    'jarak'     => $request->jarak
                ]
            );

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

            // simpan foto (base64)
            $image = $request->foto;
            $imageName = time().".png";
            Storage::put("public/absensi/".$imageName, base64_decode(explode(",", $image)[1]));

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
}

