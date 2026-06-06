<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function login()
    {
        $password = bcrypt("admin");
        // echo $password;
        // return response()->json($password);
        return view('login');
    }

    public function loginproses(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $role = Auth::user()->role;

            if ($role == 'Admin') {
                return redirect('admin')->with('success', 'Login Berhasil');
            } elseif ($role == 'Kasir') {
                return redirect('kasir')->with('success', 'Login Berhasil');
            } elseif ($role == 'Owner') {
                return redirect('owner')->with('success', 'Login Berhasil');
            } elseif ($role == 'Gudang') {
                return redirect('gudang')->with('success', 'Login Berhasil');
            }
        } else {
            return back()->with('error', 'Email atau Password Salah');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login')->with('success', 'Logout berhasil');
    }
}
