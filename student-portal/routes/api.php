<?php

use App\Http\Controllers\Api\PublicContactController;
use Illuminate\Support\Facades\Route;

Route::post('/public/contact', [PublicContactController::class, 'store'])
    ->middleware('throttle:20,1')
    ->name('api.public.contact');
