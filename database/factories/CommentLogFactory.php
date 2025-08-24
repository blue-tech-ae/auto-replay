<?php

namespace Database\Factories;

use App\Models\CommentLog;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentLogFactory extends Factory
{
    protected $model = CommentLog::class;

    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'post_id' => (string) $this->faker->uuid(),
            'comment_id' => (string) $this->faker->uuid(),
            'status' => $this->faker->randomElement(['sent', 'failed', 'skipped']),
            'error_message' => $this->faker->optional(0.3)->sentence(),
            'sent_at' => $this->faker->optional()->dateTimeBetween('-2 days', 'now'),
        ];
    }
}

