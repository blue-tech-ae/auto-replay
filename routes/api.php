<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemplateController;

Route::post('/templates', [TemplateController::class, 'save']);

