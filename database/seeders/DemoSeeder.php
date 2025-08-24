<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Shop;
use App\Models\Page;
use App\Models\Post;
use App\Models\Plan;
use App\Models\PageSubscription;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create(['email' => 'demo@example.com']);
        $shop = Shop::factory()->create(['owner_id' => $user->id, 'name' => 'Demo Shop']);
        $page = Page::factory()->create(['shop_id' => $shop->id, 'name' => 'Demo Page']);
        Post::factory()->count(6)->create(['page_id' => $page->id]);
        $plan = Plan::factory()->create(['name' => 'Pro', 'max_active_posts' => 25]);
        PageSubscription::factory()->create(['page_id' => $page->id, 'plan_id' => $plan->id]);
    }
}
