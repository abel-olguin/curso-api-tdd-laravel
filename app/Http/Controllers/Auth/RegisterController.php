<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function store(CreateUserRequest $request)
    {
        return transactional(function () use ($request) {
            $user = User::create($request->all());
            $user->assignRole(Roles::USER->value);

            return jsonResponse(data: ['user' => UserResource::make($user)]);
        });
    }
}
