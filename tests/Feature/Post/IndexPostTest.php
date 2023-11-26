<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\TestCase;

class IndexPostTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Post $post;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->posts = Post::factory()->count(30)->create();
    }

    /** @test */
    public function authenticated_user_can_see_all_posts()
    {
        Passport::actingAs($this->user);

        $this->getJson(route('posts.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'body',
                        'created_at',
                        'user' => [
                            'id',
                            'name',
                            'email',
                        ]
                    ]
                ]
            ])
            ->assertJson(
                fn(AssertableJson $json) => $json->hasAll(['data', 'links', 'meta'])
                    ->first(
                        fn (AssertableJson $json) => $json->has(20)
                            ->etc()
                    )
            );
    }

    /** @test */
    public function unauthenticated_user_can_not_see_all_posts()
    {
        $this->getJson(route('posts.index'))
            ->assertUnauthorized();
    }
}
