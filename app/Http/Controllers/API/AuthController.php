<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Nexmo\Laravel\Facade\Nexmo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request){
        $this->validate($request,[
            'name' => ['required', 'string', 'max:255'],
            'telepon' => ['required', 'string', 'max:13'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'max:30', 'string', 'alpha_dash', 'unique:users,username'],
            'image_url' => ['required', 'string'],
            'password' => ['required']
        ]);

        $user = User::create([
            'name' => $request->name,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'username' => $request->username,
            'image_url' => $request->image_url,
            'password' => Hash::make($request->password) 
        ]);

        $token = $user->createToken('testSkill')->plainTextToken;

        Nexmo::message()->send([
            'to' => $request->telepon,
            'from' => 'Admin',
            'text' => 'Registrasi sukses, simpan token berikut ini '.$token
        ]);

        $response = [
            'status' => 'Sukses',
            'message' => 'Registrasi Sukses',
            'token' => $token,
            'data' => $user
        ];

        return response($response, 201);
    }

    public function login(Request $request){
        $this->validate($request,[
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $response = [
                'status' => 'Login Gagal',
                'message' => 'Email atau password salah!'
            ];
            return response($response, 401);
        }

        $token = $user->createToken('testSkill')->plainTextToken;

        $response = [
            'status' => 'Success',
            'message' => 'Login sukses!',
            'token' => $token
        ];

        return response($response, 201);
        
    }
}
