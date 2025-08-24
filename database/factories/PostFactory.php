<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'post_id' => Str::uuid()->toString(),
            'permalink_url' => fake()->url(),
            'title' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}

