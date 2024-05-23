<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    protected $apiBase = 'api/v1';

    protected function apiAs(User $user, string $method, string $uri, array $data = [])
    {
        $headers = [
            'Authorization' => 'Bearer ' . JWTAuth::fromUser($user)
        ];
        return $this->json($method, $uri, $data, $headers);
    }
}
