<?php

use App\Models\User;
use App\Models\Shop;
use App\Models\Page;
use App\Models\Post;
use App\Models\Plan;
use App\Models\PageSubscription;
use App\Models\CommentLog;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{deleteJson, postJson, patchJson, getJson};

it('deletes a page and related records', function(){
    $user = User::factory()->create();
    $shop = Shop::factory()->create(['owner_id'=>$user->id]);
    $page = Page::factory()->create(['shop_id'=>$shop->id]);

    $posts = Post::factory()->count(3)->create(['page_id'=>$page->id]);
    CommentLog::factory()->count(2)->create(['page_id'=>$page->id, 'post_id'=>$posts->first()->post_id]);

    Sanctum::actingAs($user);

    deleteJson("/api/pages/{$page->page_id}")
        ->assertOk()
        ->assertJson(['deleted'=>true]);

    expect(Page::whereKey($page->id)->exists())->toBeFalse();
    expect(Post::where('page_id',$page->id)->count())->toBe(0);
    expect(CommentLog::where('page_id',$page->id)->count())->toBe(0);
});

it('resubscribes a page webhooks (requires manage)', function(){
    $user = User::factory()->create();
    $shop = Shop::factory()->create(['owner_id'=>$user->id]);
    $page = Page::factory()->create(['shop_id'=>$shop->id]);

    Sanctum::actingAs($user);

    $this->mock(\App\Services\GraphService::class, function($m){
        $m->shouldReceive('subscribePageWebhooks')->once()->andReturn(['success'=>true]);
    });

    postJson("/api/pages/{$page->page_id}/resubscribe")->assertOk();
});

it('bulk enables posts with plan limit', function(){
    $user = User::factory()->create();
    $shop = Shop::factory()->create(['owner_id'=>$user->id]);
    $page = Page::factory()->create(['shop_id'=>$shop->id]);

    $plan = Plan::factory()->create(['max_active_posts'=>2, 'daily_private_replies'=>100, 'monthly_private_replies'=>1000]);
    PageSubscription::factory()->create(['page_id'=>$page->id, 'plan_id'=>$plan->id, 'starts_at'=>now()->toDateString(), 'ends_at'=>null]);

    $p1 = Post::factory()->create(['page_id'=>$page->id, 'is_active'=>false]);
    $p2 = Post::factory()->create(['page_id'=>$page->id, 'is_active'=>false]);
    $p3 = Post::factory()->create(['page_id'=>$page->id, 'is_active'=>false]);

    Sanctum::actingAs($user);

    patchJson("/api/pages/{$page->page_id}/posts/bulk-toggle", [
        'post_ids' => [$p1->post_id, $p2->post_id, $p3->post_id],
        'enable' => true
    ])->assertOk()->assertJson(['updated'=>2]);

    expect(Post::where('id',$p1->id)->value('is_active'))->toBeTrue();
    expect(Post::where('id',$p2->id)->value('is_active'))->toBeTrue();
    expect(Post::where('id',$p3->id)->value('is_active'))->toBeFalse();
});

it('bulk applies template to many posts', function(){
    $user = User::factory()->create();
    $shop = Shop::factory()->create(['owner_id'=>$user->id]);
    $page = Page::factory()->create(['shop_id'=>$shop->id]);
    $posts = Post::factory()->count(3)->create(['page_id'=>$page->id, 'is_active'=>true]);

    Sanctum::actingAs($user);

    postJson("/api/pages/{$page->page_id}/posts/bulk-template", [
        'post_ids' => $posts->pluck('post_id')->all(),
        'private_reply_template' => "Price: {price}\n{post_url}",
        'public_reply_template' => 'Thanks for your comment!'
    ])->assertOk()->assertJson(['updated'=>3]);
});

it('lists logs with filters', function(){
    $user = User::factory()->create();
    $shop = Shop::factory()->create(['owner_id'=>$user->id]);
    $page = Page::factory()->create(['shop_id'=>$shop->id]);

    CommentLog::factory()->create(['page_id'=>$page->id, 'status'=>'sent', 'comment_id'=>'c1', 'post_id'=>'p1']);
    CommentLog::factory()->create(['page_id'=>$page->id, 'status'=>'failed', 'comment_id'=>'c2', 'post_id'=>'p2', 'error_message'=>'Quota exceeded']);

    Sanctum::actingAs($user);

    getJson("/api/pages/{$page->page_id}/logs?status=failed&q=Quota")
        ->assertOk()
        ->assertJsonFragment(['status'=>'failed'])
        ->assertJsonMissing(['status'=>'sent']);
});

it('forbids non-owner for page actions', function(){
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $shop  = Shop::factory()->create(['owner_id'=>$owner->id]);
    $page  = Page::factory()->create(['shop_id'=>$shop->id]);

    Sanctum::actingAs($other);
    deleteJson("/api/pages/{$page->page_id}")->assertForbidden();
});

