<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RssFeedController;

Route::get('/{section}', RssFeedController::class)
    ->where('section', '^[a-z]+(-[a-z]+)*$');
