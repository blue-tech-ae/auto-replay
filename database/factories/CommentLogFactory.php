<?php

namespace Database\Factories;

use App\Models\CommentLog;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CommentLogFactory extends Factory
{
    protected $model = CommentLog::class;

    public function definition(): array
    {
        return [
            'comment_id' => Str::uuid()->toString(),
            'page_id' => Page::factory(),
            'post_id' => Str::uuid()->toString(),
            'status' => 'sent',
            'error_code' => null,
            'error_message' => null,
            'sent_at' => now(),
        ];
    }
}

