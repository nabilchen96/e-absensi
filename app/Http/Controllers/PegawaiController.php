<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Auth;


class PegawaiController extends Controller
{
    public function index(){

        return view('backend.pegawai.index');
    }

    public function data(Request $request)
    {
        $keyword = $request->keyword;

        $query = DB::table('users')->where('role', 'Pegawai');

        // Jika keyword ada
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%")
                ->orWhere('email', 'like', "%$keyword%")
                ->orWhere('role', 'like', "%$keyword%");
            });
        }

        if(Auth::user()->role == 'Pegawai'){

            $user = $query->where('id', Auth::id())->get();

        }else{

            $user = $query->get();
        }


        return response()->json(['data' => $user]);
    }
}
