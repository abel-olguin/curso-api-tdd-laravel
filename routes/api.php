<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/hello-world', function (Request $request) {
    return response()->json(['msg' => 'Hello World!']);
});
