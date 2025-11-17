<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Auth\{
    RegisterRequest
};
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        if ($request->filled(['username', 'email', 'password'])) {
            DB::beginTransaction();

            try {
                $newUser = User::create([
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ]);

                DB::commit();

                return $this->success('User created successfully', $newUser);

            } catch (\Exception $e) {
                DB::rollback();
                return $this->error('User creation failed');
            }
        } else {
            return $this->error('Incomplete credentials');
        }
    }

    public function login() {
        //
    }

    public function logout() {
        //
    }

    public function getAccountInfo() {
        //
    }

    public function editAccountInfo() {
        //
    }

    public function showUser() {
        //
    }
}
