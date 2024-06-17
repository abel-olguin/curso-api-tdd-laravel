<?php


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotFoundTest extends TestCase
{
    #[Test]
    public function not_found_error_must_have_the_correct_structure(): void
    {
        $response = $this->get("{$this->apiBase}/not-found");

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'data', 'message', 'status', 'errors'
        ]);
        $response->assertJsonPath('status', 404);
    }
}
