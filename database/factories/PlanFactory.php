<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Basic','Pro','Scale','Ultra']).' '.$this->faker->randomDigit(),
            'max_active_posts' => $this->faker->randomElement([5,25,100]),
            'daily_private_replies' => $this->faker->numberBetween(100, 5000),
            'monthly_private_replies' => $this->faker->numberBetween(1500, 80000),
        ];
    }
}
