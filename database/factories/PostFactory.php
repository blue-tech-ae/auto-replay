<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'post_id' => function (array $attrs) {
                $page = Page::find($attrs['page_id']);

                return $page
                    ? ($page->page_id . '_' . (string) fake()->numberBetween(1000000, 9999999))
                    : (string) fake()->uuid();
            },
            'title' => $this->faker->sentence(3),
            'is_active' => false,
        ];
    }
}

