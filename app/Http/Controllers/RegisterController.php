<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\BaseController;

class RegisterController extends BaseController
{
    /**
     * Register api
     */
    public function register(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'min:6'],
            'confirm_password' => ['required', 'same:password'],
        ]);

        $existingUser = User::where('email', $validatedData['email'])->first();
        if ($existingUser !== null) {
            return $this->sendError('Email already exists.', [], 409);
        }

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User registered successfully.', 200);
    }

    public function login(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $validatedData['email'])->first();
        if (! $user || ! Hash::check($validatedData['password'], $user->password)) {
            return $this->sendError('Unauthorized', ['error' => 'Invalid credentials'], 401);
        }

        $existingToken = $user->tokens()->first();
        if ($existingToken) {
            $existingToken->delete();
        }

        $success['token'] = $user->createToken('dropbox')->plainTextToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User login successfully.', 200);
    }
}
