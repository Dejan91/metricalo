<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdatePostTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private Post $post;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->post = Post::factory()->create([
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function authenticated_user_can_update_his_own_post()
    {
        Passport::actingAs($this->user);

        $this->putJson(
            route('posts.update', ['post' => $this->post->slug]
            ),
            [
                'title' => $changedTitle = $this->faker->title(),
                'body'  => $changedBody = $this->faker->text(),
                'slug'  => $changedSlug = $this->faker->slug(),
            ]
        )
            ->assertOk();

        $this->assertDatabaseMissing('posts', [
            'title' => $this->post->title,
            'body'  => $this->post->body,
            'slug'  => $this->post->slug,
        ]);

        $this->assertDatabaseHas('posts', [
            'title' => $changedTitle,
            'body'  => $changedBody,
            'slug'  => $changedSlug,
        ]);
    }

    /** @test */
    public function unauthenticated_user_can_not_edit_post()
    {
        $this->putJson(
            route('posts.update', ['post' => $this->post->slug])
        )
            ->assertUnauthorized();
    }

    /** @test */
    public function authenticated_user_can_not_update_other_users_posts()
    {
        $anotherUser = User::factory()->create();
        $anotherUserPost = Post::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        Passport::actingAs($this->user);

        $this->putJson(
            route('posts.update', ['post' => $anotherUserPost->slug])
        )
            ->assertForbidden();
    }

    /** @test */
    public function updated_slug_must_be_unique()
    {
        $anotherUser = User::factory()->create();
        $anotherUserPost = Post::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        Passport::actingAs($this->user);

        $this->putJson(
            route('posts.update', ['post' => $this->post->slug]),
            ['slug' => $anotherUserPost->slug]
        )
            ->assertInvalid('slug')
            ->assertUnprocessable();
    }
}
