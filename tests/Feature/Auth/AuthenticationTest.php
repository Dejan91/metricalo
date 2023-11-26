<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'password' => Hash::make('correct-password')
        ]);
    }

    /** @test */
    public function users_can_get_token_with_correct_credentials()
    {
        $this->postJson(route('auth.login'), [
            'email'    => $this->user->email,
            'password' => 'correct-password',
        ])
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->has('token'));

        $this->assertDatabaseHas('oauth_access_tokens', [
            'user_id' => $this->user->id,
            'revoked' => false,
        ]);
    }

    /** @test */
    public function validation_error_is_returned_with_incorrect_email()
    {
        $this->postJson(route('auth.login'), [
            'email'    => 'incorect@email.com',
            'password' => 'correct-password',
        ])
            ->assertInvalid('email')
            ->assertJsonMissing(['token']);

        $this->assertDatabaseMissing('oauth_access_tokens', [
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function validation_error_is_returned_with_incorrect_password()
    {
        $this->postJson(route('auth.login'), [
            'email'    => $this->user->email,
            'password' => 'incorrect-password',
        ])
            ->assertInvalid('password')
            ->assertJsonMissing(['token']);

        $this->assertDatabaseMissing('oauth_access_tokens', [
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function token_is_revoked_when_user_log_out()
    {
        $response = $this->postJson(route('auth.login'), [
            'email'    => $this->user->email,
            'password' => 'correct-password',
        ])
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->has('token'));

        $this->assertDatabaseHas('oauth_access_tokens', [
            'user_id' => $this->user->id,
            'revoked' => false,
        ]);

        $this->withToken($response->getData()->token)
            ->postJson(route('auth.logout'))
            ->assertOk()
            ->assertExactJson([
                'message' => 'You have been successfully logged out!',
            ]);

        $this->assertDatabaseHas('oauth_access_tokens', [
            'user_id' => $this->user->id,
            'revoked' => true,
        ]);
    }
}
