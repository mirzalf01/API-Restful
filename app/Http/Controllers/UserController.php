<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        $users = User::get();

        $response = [
            'status' => 'Sukses',
            'message' => 'Get user sukses',
            'data' => $users
        ];

        return response($response, 200);
    }

    public function show($id){
        $user = User::find($id);

        if ($user) {
            $response = [
                'status' => 'Sukses',
                'message' => 'Get user with id success',
                'data' => $user
            ];
            return response($response, 200);
        }
        else{
            $response = [
                'status' => 'Gagal',
                'message' => 'User not found',
            ];
            return response($response, 404);
        }
    }

    public function update(Request $request, $id){
        
        $this->validate($request,[
            'name' => ['string', 'max:255'],
            'telepon' => ['string', 'max:13'],
            'email' => ['email', 'unique:users,email'],
            'username' => ['max:30', 'string', 'alpha_dash', 'unique:users,username'],
            'image_url' => ['string'],
            'old_password' => ['required']
        ]);
        
        $user = User::find($id);

        if (!$user) {
            $response = [
                'status' => 'Gagal',
                'message' => 'User not found',
            ];
            return response($response, 404);
        }
        
        if (!Hash::check($request->old_password, $user->password)) {
            $response = [
                'status' => 'Gagal',
                'message' => 'Old password tidak cocok!',
            ];
            return response($response, 404);
        }

        $user->name = $request->name ? $request->name : $user->name;
        $user->telepon = $request->telepon ? $request->telepon : $user->telepon;
        $user->email = $request->email ? $request->email : $user->email;
        $user->username = $request->username ? $request->username : $user->username;
        $user->image_url = $request->image_url ? $request->image_url : $user->image_url;
        $user->password = $request->password ? Hash::make($request->password) : $user->password;
        $user->save();

        $response = [
            'status' => 'Sukses',
            'message' => 'Update data sukses!',
            'data' => $user
        ];
        return response($response, 200);
    }
}
