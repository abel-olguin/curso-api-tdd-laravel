<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;

class ProfileController extends Controller
{
    public function update(UpdateUserRequest $request)
    {
        auth()->user()->update($request->validated());
        $user = UserResource::make(auth()->user()->fresh());
        $token = auth()->login(auth()->user());
        return jsonResponse(compact('user', 'token'));
    }
}
