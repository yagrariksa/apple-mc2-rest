<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $u = User::where('email', $request->email)->first();

        $rules = [
            'email' => 'required|string',
            'password' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'email and password are required',
                'data' => $validator->errors()
            ], 422);
        }

        if ($u) {
            if (Hash::check($request->password, $u->password)) {
                $u->api_token = Str::random(24);
                $u->save();
                return response()->json([
                    'message' => 'success login',
                    'data' => [
                        'api_token' => $u->api_token,
                        'user' => new UserResource($u)
                    ]
                ], 201);
            }
        }

        return response()->json([
            'message' => 'you are not authenticated, please correct your email or password',
            'data' => []
        ], 200);
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'you are not successfully register, please try again',
                'data' => $validator->errors()
            ], 422);
        }

        $u = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(24),
        ]);

        return response()->json([
            'message' => 'success create new user',
            'data' => [
                'api_token' => $u->api_token,
                'user' => new UserResource($u)
            ]
        ], 201);
    }
}
