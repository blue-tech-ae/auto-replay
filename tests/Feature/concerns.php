<?php
use App\Models\User;
use App\Models\Shop;
use App\Models\Page;

function actingAsOwnerWithPage(): array {
    $user = User::factory()->create();
    $shop = Shop::factory()->create(['owner_id' => $user->id]);
    $page = Page::factory()->create(['shop_id' => $shop->id]);
    return [$user, $shop, $page];
}

