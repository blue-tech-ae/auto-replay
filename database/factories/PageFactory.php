<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $pid = (string) $this->faker->numberBetween(10000000000, 99999999999);

        return [
            'shop_id' => Shop::factory(),
            'page_id' => $pid,
            'name' => $this->faker->company(),
            'access_token' => 'EAAB' . base64_encode($this->faker->uuid()),
        ];
    }
}

