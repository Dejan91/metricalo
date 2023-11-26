<?php

namespace App\Models;

use App\Services\PostSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
      'title',
      'slug',
      'body',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($post) {
            if (! $post->slug) {
                $post->slug = PostSlug::generate($post->id, $post->title);
                $post->save();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
