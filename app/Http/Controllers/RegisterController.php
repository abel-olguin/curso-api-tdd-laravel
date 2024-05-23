<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function store(CreateUserRequest $request)
    {
        $user = User::create($request->all());

        return jsonResponse(data: ['user' => UserResource::make($user)]);
    }
}
