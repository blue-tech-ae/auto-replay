<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Plan::firstOrCreate(['name' => 'Basic'], [
            'max_active_posts' => 5,
            'daily_private_replies' => 100,
            'monthly_private_replies' => 1500,
        ]);

        \App\Models\Plan::firstOrCreate(['name' => 'Pro'], [
            'max_active_posts' => 25,
            'daily_private_replies' => 800,
            'monthly_private_replies' => 12000,
        ]);

        \App\Models\Plan::firstOrCreate(['name' => 'Scale'], [
            'max_active_posts' => 100,
            'daily_private_replies' => 5000,
            'monthly_private_replies' => 80000,
        ]);
    }
}

