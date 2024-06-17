<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    #[Test]
    public function authentication_error_must_have_the_correct_structure(): void
    {
        $response = $this->get(route('restaurants.index'));

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'data', 'message', 'status', 'errors'
        ]);
        $response->assertJsonPath('status', 401);
    }
}
