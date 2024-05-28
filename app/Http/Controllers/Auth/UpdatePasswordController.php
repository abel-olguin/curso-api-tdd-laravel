<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordController extends Controller
{

    public function update(UpdatePasswordRequest $request)
    {
        auth()->user()->update([
            'password' => Hash::make($request->get('password'))
        ]);

        return jsonResponse();
    }
}
