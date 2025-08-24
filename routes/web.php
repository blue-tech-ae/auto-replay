<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacebookAuthController;
use App\Http\Controllers\FacebookWebhookController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/auth/facebook/redirect', [FacebookAuthController::class, 'redirect']);
Route::get('/auth/facebook/callback', [FacebookAuthController::class, 'callback']);

Route::get('/webhooks/facebook',  [FacebookWebhookController::class, 'verify']);
Route::post('/webhooks/facebook', [FacebookWebhookController::class, 'handle']);

