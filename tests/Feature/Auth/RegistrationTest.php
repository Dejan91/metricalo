<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_with_valid_credentials()
    {
        $this->assertDatabaseMissing('users', [
            'name'  => $name = 'Valid name',
            'email' => $email = 'valid@email.com',
        ]);

        $this->postJson(route('auth.register'), [
            'name'                  => $name,
            'email'                 => $email,
            'password'              => 'valid-password',
            'password_confirmation' => 'valid-password',
        ])
            ->assertCreated()
            ->assertJson(
                fn(AssertableJson $json) => $json->where('name', $name)
                    ->where('name', $name)
                    ->where('email', $email)
                    ->etc()
            );

        $this->assertDatabaseHas('users', [
            'name'  => $name,
            'email' => $email,
        ]);
    }
}
