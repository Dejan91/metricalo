<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StorePostTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_create_post()
    {
        Passport::actingAs($this->user);

        $this->postJson(route('posts.store'), [
            'title' => $title = $this->faker->title(),
            'body'  => $body = $this->faker->text(),
        ])
            ->assertCreated();

        $this->assertDatabaseHas('posts', [
            'title' => $title,
            'body'  => $body,
        ]);
    }

    /** @test */
    public function unauthenticated_user_can_not_create_post()
    {
        $this->getJson(route('posts.index'))
            ->assertUnauthorized();
    }

    /** @test */
    public function slug_cen_be_set_manually()
    {
        Passport::actingAs($this->user);

        $post = $this->postJson(route('posts.store'), [
            'title' => $this->faker->title(),
            'body'  => $this->faker->text(),
            'slug'  => 'manually-set-slug',
        ])
            ->assertCreated()
            ->getData();

        $this->assertDatabaseHas('posts', [
            'slug'  => $post->slug,
        ]);
    }

    /** @test */
    public function if_not_provided_slug_is_generated_authomatically()
    {
        Passport::actingAs($this->user);

        $post = $this->postJson(route('posts.store'), [
            'title' => $this->faker->title(),
            'body'  => $this->faker->text(),
        ])
            ->assertCreated()
            ->getData();

        $generatedSlug = Str::slug($post->title) . '-' . $post->id;

        $this->assertDatabaseHas('posts', [
            'slug'  => $generatedSlug,
        ]);
    }

    /** @test */
    public function slug_must_be_unique()
    {
        Post::factory()->create([
            'slug' => $slug = 'created-slug',
        ]);

        Passport::actingAs($this->user);

        $this->postJson(route('posts.store'), [
            'title' => $this->faker->title(),
            'body'  => $this->faker->text(),
            'slug'  => $slug,
        ])
            ->assertUnprocessable()
            ->assertValid(['title', 'body'])
            ->assertInvalid('slug');
    }
}
