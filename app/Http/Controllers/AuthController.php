<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\{
    RegisterRequest,
    LoginRequest
};
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        if ($request->filled(['username', 'email', 'password', 'firstName', 'lastName'])) {
            DB::beginTransaction();

            try {
                $newUser = User::create([
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
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

    public function login(LoginRequest $request) {
        $credentials = $request->only('username', 'password');

        if (!auth()->attempt($credentials)) {
            return $this->error('Invalid login credentials');
        }

        $user = auth()->user();

        $token = $user->createToken('auth_token')->plainTextToken;

        $transformedUser = [
            'username' => $user->username,
            'fullName' => $user->first_name . ' ' . $user->last_name,
            'email' => $user->email,
        ];

        return $this->success('Login successful', [
            'token' => $token, 
            'user' => $transformedUser
        ]);
    }

    public function logout(Request $request) {
        $user = $request->user();

        if ($user->tokens()->count() > 0) {
            $user->tokens()->delete();
        }

        return $this->success('Logout successful');
    }
}
