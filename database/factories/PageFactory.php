<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'page_id' => fake()->unique()->numerify('########'),
            'name' => fake()->company(),
            'access_token' => Str::random(40),
        ];
    }
}

