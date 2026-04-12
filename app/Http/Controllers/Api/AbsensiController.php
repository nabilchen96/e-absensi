<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $query = DB::table('absensis as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('u.role', 'Pegawai')
            ->orderBy('a.id', 'DESC');

        // 🔍 keyword
        if ($keyword) {
            $query->where('u.name', 'like', "%$keyword%");
        }

        // 👑 ADMIN → semua data
        if (Auth::user()->role == 'Admin') {

            $data = $query->select('a.*', 'u.name')->get();

        }
        // 🏢 OPD → pegawai satu unit kerja
        elseif (Auth::user()->role == 'OPD') {

            $idUnitKerja = Auth::user()->id_unit_kerja_pandu;

            $data = $query
                ->join('lokasi_kerja_users as lku', 'lku.id_user', '=', 'u.id')
                ->join('lokasi_kerjas as lk', 'lk.id', '=', 'lku.id_lokasi_kerja')
                ->where('lk.id_pandu', $idUnitKerja)
                ->select('a.*', 'u.name', 'lk.lokasi_kerja')
                ->get();

        }
        // 👤 PEGAWAI → data sendiri
        else {

            $data = $query
                ->where('a.user_id', Auth::id())
                ->select('a.*', 'u.name')
                ->get();
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto'      => 'required',
            'latitude'  => 'required',
            'longitude' => 'required',
            'jarak'     => 'required'
        ],[
            'foto.required'     => 'Klik Ambil Foto Terlebih Dahulu',
            'jarak.required'    => 'Jarak Wajib Diisi, Pastikan Lokasi Kerja Memiliki Koordinat'
        ]);

        if ($validator->fails()) {

            $data = [
                'responCode' => 0,
                'respon' => $validator->errors()
            ];

        }else{

            if ($request->hasFile('foto')) {
                // ✅ HANDLE FILE UPLOAD (Postman / form-data)
                $file = $request->file('foto');

                $imageName = time() . '.' . $file->getClientOriginalExtension();

                $path = public_path('absensi');
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }

                $file->move($path, $imageName);

            } else {
                // ✅ HANDLE BASE64 (dari kamera / mobile / web)
                $image = $request->foto;

                if (str_contains($image, ',')) {
                    $image = explode(',', $image)[1];
                }

                $imageData = base64_decode($image);

                $imageName = time() . '.png';

                $path = public_path('absensi');
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }

                file_put_contents($path . '/' . $imageName, $imageData);
            }

            Absensi::create([
                'foto'      => $imageName,
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'user_id'   => auth()->user()->id ?? 1,
                'datetime'  => now(),
                'jarak'     => $request->jarak
            ]);


            $data = [
                'responCode' => 1,
                'respon' => 'Data Sukses Disimpan'
            ];
        }


        return response()->json($data);
    }
}
