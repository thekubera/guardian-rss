<?php

use App\Http\Controllers\RssFeedController;
use Illuminate\Support\Facades\Route;

Route::get('/{section}', RssFeedController::class)
    ->where('section', '^[a-z]+(-[a-z]+)*$');
