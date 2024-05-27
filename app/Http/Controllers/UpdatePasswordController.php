<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
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
