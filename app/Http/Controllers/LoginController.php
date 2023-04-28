<?php

namespace App\Http\Controllers;

use App\AnggotaUkm;
use App\Nasabah;
use App\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class LoginController extends Controller
{

    /** fungsi login */
    public function getLogin()
    {
        return view('login');
    }

    public function postLogin(Request $request)
    {
        //LAKUKAN PENGECEKAN, JIKA INPUTAN DARI USERNAME FORMATNYA ADALAH EMAIL, MAKA KITA AKAN MELAKUKAN PROSES AUTHENTICATION MENGGUNAKAN EMAIL, SELAIN ITU, AKAN MENGGUNAKAN USERNAME
        $loginType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        //TAMPUNG INFORMASI LOGINNYA, DIMANA KOLOM TYPE PERTAMA BERSIFAT DINAMIS BERDASARKAN VALUE DARI PENGECEKAN DIATAS
        $login = [
            $loginType => $request->username,
            'password' => $request->password
        ];
        if (Auth::guard('user')->attempt($login)) {
            return response()->json([
                'success' => true,
                'role' => auth()->guard('user')->user()->role,
                'message' => 'Login Sukses!'
            ]);
        } else if (Auth::guard('pelanggan')->attempt($login)) {
            return response()->json([
                'success' => true,
                'role' => auth()->guard('pelanggan')->user()->role,
                'message' => 'Login Sukses!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password tidak terdaftar!'
            ]);
        }
    }

    public function logout()
    {
        if (Auth::guard('user')->check() || Auth::guard('pelanggan')->check()) {
            Auth::guard('user')->logout();
            Auth::guard('pelanggan')->logout();
            Session::flush();
            return redirect()->route('login');
        }
    }
}
