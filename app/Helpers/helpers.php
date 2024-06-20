<?php

use App\Enums\Roles;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

function jsonResponse($data = [], $status = 200, $message = 'OK', $errors = []): \Illuminate\Http\JsonResponse
{
    return response()->json(compact('data', 'status', 'message', 'errors'), $status);
}


function transactional(\Closure $callback)
{
    DB::beginTransaction();
    try {
        $result = $callback();
        DB::commit();
        return $result;
    } catch (\Exception $exception) {
        DB::rollBack();
        //Log::error($exception->getMessage());
        dd($exception);
        return jsonResponse(message: 'Ocurrio un error', status: 500);
    }
}
