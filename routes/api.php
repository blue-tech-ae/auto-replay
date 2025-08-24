<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriptionController;
use App\Models\Shop;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('shops', function (Request $r) {
        return Shop::where('owner_id', $r->user()->id)->with('pages')->get();
    });

    Route::post('shops', function (Request $r) {
        $data = $r->validate(['name' => 'required|string|max:120']);
        $shop = Shop::create(['owner_id' => $r->user()->id, 'name' => $data['name']]);
        $shop->users()->attach($r->user()->id, ['role' => 'owner']);
        return $shop;
    });

    Route::middleware('can:isAdmin')->apiResource('plans', PlanController::class);

    Route::prefix('pages/{page_id}')->middleware('page.owner')->group(function () {
        Route::post('posts/import', [PostController::class, 'import']);
        Route::get('posts', [PostController::class, 'index']);
        Route::patch('posts/{post_id}/toggle', [PostController::class, 'toggle']);

        Route::get('posts/{post_id}/template', [TemplateController::class, 'show']);
        Route::post('posts/{post_id}/template', [TemplateController::class, 'store']);
        Route::put('posts/{post_id}/template', [TemplateController::class, 'update']);
        Route::delete('posts/{post_id}/template', [TemplateController::class, 'destroy']);

        Route::post('subscribe', [SubscriptionController::class, 'subscribe']);
        Route::post('unsubscribe', [SubscriptionController::class, 'unsubscribe']);
        Route::get('subscription', [SubscriptionController::class, 'current']);
        Route::get('quota', [SubscriptionController::class, 'quota']);
    });
});
