<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Auth;

class UserController extends Controller
{
    public function index(){

        return view('backend.user.index');
    }

    public function data(Request $request)
    {
        $keyword = $request->keyword;

        $query = DB::table('users')
                    ->leftjoin('lokasi_kerjas', 'lokasi_kerjas.id_pandu', '=', 'users.id_unit_kerja_pandu')
                    ->select(
                        'users.*', 
                        'lokasi_kerjas.lokasi_kerja'
                    )
                    ->whereNotIn('role', ['Pegawai']);

        // Jika keyword ada
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%")
                ->orWhere('email', 'like', "%$keyword%")
                ->orWhere('role', 'like', "%$keyword%");
            });
        }

        if(Auth::user()->role == 'Admin'){

            $user = $query->get();

        }else{

            $user = $query->where('users.id', Auth::id())->get();
        }

        return response()->json(['data' => $user]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password'  => 'required|min:8',
            'email'     => 'unique:users',
            'role'      => 'required',
            'name'      => 'required'
        ]);

        if ($validator->fails()) {
            $data = [
                'responCode' => 0,
                'respon' => $validator->errors()
            ];
        } else {
            User::create([
                'name' => $request->name,
                'role' => $request->role,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $data = [
                'responCode' => 1,
                'respon' => 'Data Sukses Disimpan'
            ];
        }

        return response()->json($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $request->id,
        ]);

        if ($validator->fails()) {
            $data = [
                'responCode' => 0,
                'respon' => $validator->errors()
            ];
        } else {

            $user = User::find($request->id);
            $user->update([
                'name' => $request->name,
                'role' => $request->role ?? $user->role,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
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

        $data = User::find($request->id)->delete();

        $data = [
            'responCode' => 1,
            'respon' => 'Data Sukses Dihapus'
        ];

        return response()->json($data);
    }
}
