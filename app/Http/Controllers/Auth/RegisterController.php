<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class RegisterController extends Controller
{
    public function store(CreateUserRequest $request)
    {
        $user = User::create($request->all());

        return jsonResponse(data: ['user' => UserResource::make($user)]);
    }
}
