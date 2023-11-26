<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DestroyPostTest extends TestCase
{
    use RefreshDatabase;

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
    public function authenticated_user_can_delete_his_own_post()
    {
        $this->assertDatabaseHas('posts', [
            'user_id' => $this->user->id,
            'title'   => $this->post->title,
            'body'    => $this->post->body,
        ]);

        Passport::actingAs($this->user);

        $this->deleteJson(
            route('posts.destroy', ['post' => $this->post->slug])
        )
            ->assertOk();

        $this->assertDatabaseMissing('posts', [
            'user_id' => $this->user->id,
            'title'   => $this->post->title,
            'body'    => $this->post->body,
        ]);
    }

    /** @test */
    public function unauthenticated_user_can_not_delete_post()
    {
        $this->deleteJson(
            route('posts.destroy', ['post' => $this->post->slug])
        )
            ->assertUnauthorized();
    }

    /** @test */
    public function authenticated_user_can_not_delete_other_users_posts()
    {
        $anotherUser = User::factory()->create();
        $anotherUserPost = Post::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        Passport::actingAs($this->user);

        $this->putJson(
            route('posts.destroy', ['post' => $anotherUserPost->slug])
        )
            ->assertForbidden();
    }
}
