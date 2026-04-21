<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_login_with_correct_data(): void
    {
        User::create([
            'name' => 'test',
            'email' => 'email@example.com',
            'password' => 'password123',
        ]);
        $request = $this->postJson('api/login', [
            'email' => 'email@example.com',
            'password' => 'password123',
        ]);
        $request->assertStatus(200);
    }

    #[Test]
    public function user_cannot_login_with_incorrect_data(): void
    {
        $request = $this->postJson('api/login', [
            'email' => 'email@example.com',
            'password' => 'password',
        ]);
        $request->assertStatus(401);
    }
}
