<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AbsensiController extends Controller
{
    public function index()
    {
        return view('backend.absensi.index');
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
            ->orderBy('id', 'DESC')
            ->get();

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
    
            Absensi::Create(
                [
                    'foto'      => $imageName,
                    'latitude'  => $request->latitude,
                    'longitude' => $request->longitude,
                    'user_id'   => $user->id,
                    'datetime'  => now(),
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

