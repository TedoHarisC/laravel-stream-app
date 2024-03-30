<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index()
    {
        return view('member.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone_number' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $data = $request->except("_token");

        // Mengecek apakah email yang didaftarkan sudah ada atau belum, kalau ada ditolak
        $isEmailExist = User::where('email', $request->email)->exists();

        if ($isEmailExist) {
            return back()
                ->withErrors([
                    'email' => 'This email already exist'
                ])
                ->withInput();
        }

        $data['role'] = 'member';
        $data['password'] = Hash::make($request->password);

        User::create($data);

        return back();
        //return redirect()->route('member.login');
    }
}
