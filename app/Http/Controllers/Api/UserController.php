<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $idUser = $request->id_pandu;
        $email  = $request->email;
        
        $query  = DB::table('users')
                ->select(
                    'users.name',
                    'users.role',
                    'users.email',
                    'users.created_at', 
                    'users.id',
                    'users.id_pandu'
                )
                ->where('role', 'Pegawai');

        
        // Filter Nama User
        if (!empty($idUser)) {
            $query->where('users.id_pandu', $idUser);
        }

        // Filter Email user
        if (!empty($email)) {
            $query->where('users.email', $email);
        }

        $query->orderBy('users.name', 'asc');

        return response()->json($query->get());
    }
}
