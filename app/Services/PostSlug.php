<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;

class PostSlug
{
    public static function generate(int $id, string $title): string
    {
        $slug = Str::slug($title) . '-' . $id;

        while (Post::where('slug', $slug)->exists()) {
            $id = $id + 1;

            $slug = Str::beforeLast($slug, '-') . '-' . $id;
        }

        return $slug;
    }
}
