<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ShowPostTest extends TestCase
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
    public function authenticated_user_can_show_his_own_post()
    {
        Passport::actingAs($this->user);

        $this->getJson(
            route('posts.show', ['post' => $this->post->slug])
        )
            ->assertOk()
            ->assertExactJson([
                'id'         => $this->post->id,
                'title'      => $this->post->title,
                'slug'       => $this->post->slug,
                'body'       => $this->post->body,
                'created_at' => $this->post->created_at->format('Y-m-d H-i-s'),
            ]);
    }

    /** @test */
    public function unauthenticated_user_can_not_show_any_post()
    {
        $this->getJson(
            route('posts.show', ['post' => $this->post->slug])
        )
            ->assertUnauthorized();
    }

    /** @test */
    public function authenticated_user_can_show_other_users_posts()
    {
        $anotherUser = User::factory()->create();
        $anotherUserPost = Post::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        Passport::actingAs($this->user);

        $this->getJson(
            route('posts.show', ['post' => $anotherUserPost->slug])
        )
            ->assertOk()
            ->assertExactJson([
                'id'         => $anotherUserPost->id,
                'title'      => $anotherUserPost->title,
                'slug'       => $anotherUserPost->slug,
                'body'       => $anotherUserPost->body,
                'created_at' => $anotherUserPost->created_at->format('Y-m-d H-i-s'),
            ]);
    }
}
