<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        #check jika username telah digunakan
        if (User::where('username', $data['username'])->exists()) {
            throw new HttpResponseException(response([
                'errors' => [
                    'username' => [
                        'The username already exists.'
                    ]
                ]
            ],400));
        }

        $user = new User($data);
        $user->password = bcrypt($data['password']);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): UserResource{
        $data = $request->validated();

        $user = User::where('username', $data['username'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Username or password wrong.'
                    ]
                ]
            ],401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();
        return new UserResource($user);
    }

    public function get(Request $request): UserResource{
        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request): UserResource
    {
        $data = $request->validated(); #ambil data yang terfalidasi
        $user = Auth::user(); #cek apakah sudah login?

        #isset berguna untuk cek datanya sudah ada atau belum
        if(isset($data['password'])){
            $user->password = bcrypt($data['password']);
        }

        if(isset($data['name'])){
            $user->name = $data['name'];
        }
        $user->save();
        return new UserResource($user);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user(); #cek apakah sudah login?
        $user->token = null;
        $user->save();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}
