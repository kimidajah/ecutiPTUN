<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

Route::post('/webhook/wa', [WebhookController::class, 'handleWA']);
Route::post('/webhook-wa', [WebhookController::class, 'handleWA']);

