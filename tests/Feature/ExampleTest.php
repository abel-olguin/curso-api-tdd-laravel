<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    #[Test]
    public function hello_world_route_should_return_status_success(): void
    {
        #teniendo
        // teniendo unas credenciales

        #haciendo
        $response = $this->get('api/hello-world');

        #esperando
        $response->assertJson(['msg' => 'Hello World!']);
        $response->assertJsonStructure(['msg']);
    }
}
