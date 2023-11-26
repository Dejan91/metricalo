<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Post::class, 'post');
    }

    public function index(): AnonymousResourceCollection
    {
        return PostResource::collection(
            Post::with('user')
                ->orderBy('created_at', 'DESC')
                ->paginate(20)
        );
    }

    public function store(StorePostRequest $request): PostResource
    {
        return PostResource::make(
            Auth::user()->posts()->create($request->all())
        );
    }

    public function show(Post $post): PostResource
    {
        return PostResource::make($post);
    }

    public function update(UpdatePostRequest $request, Post $post): PostResource
    {
        $post->update($request->all());

        return PostResource::make($post);
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json(['message' => 'Post deleted.']);
    }
}
