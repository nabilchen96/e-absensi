<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Models\Otp;
use App\Models\Profil;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;
use DB;
use Illuminate\Support\Carbon;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check() == true) {
            return redirect('dashboard');
        } else {
            return view('frontend.login');
        }

    }

    public function loginProses(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $identifier = $request->input('email'); // bisa email atau NIP
        $password = $request->input('password');

        // Cek apakah identifier adalah email atau NIP
        $fieldType = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';

        // Siapkan kredensial
        $credentials = [
            $fieldType => $identifier,
            'password' => $password
        ];

        // Attempt login
        if (Auth::attempt($credentials)) {
            $role = Auth::user()->role;

            return response()->json([
                'responCode' => 1,
                'respon' => $role
            ]);
        }

        // Jika gagal
        return response()->json([
            'responCode' => 0,
            'respon' => 'Email/NIP atau password salah!'
        ]);
    }

}
