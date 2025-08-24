<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TemplateController;

Route::prefix('pages/{page_id}')->group(function () {
    Route::post('posts/import', [PostController::class, 'import']);
    Route::get('posts', [PostController::class, 'index']);
    Route::patch('posts/{post_id}/toggle', [PostController::class, 'toggle']);

    Route::get('posts/{post_id}/template', [TemplateController::class, 'show']);
    Route::post('posts/{post_id}/template', [TemplateController::class, 'store']);
    Route::put('posts/{post_id}/template', [TemplateController::class, 'update']);
    Route::delete('posts/{post_id}/template', [TemplateController::class, 'destroy']);
});
