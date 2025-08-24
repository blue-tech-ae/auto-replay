<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\PageSubscription;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageSubscriptionFactory extends Factory
{
    protected $model = PageSubscription::class;

    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'plan_id' => Plan::factory(),
            'starts_at' => now()->toDateString(),
            'ends_at' => null,
        ];
    }
}

