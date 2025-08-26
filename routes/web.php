<?php

use App\Http\Controllers\AppTopCategoryController;
use App\Http\Middleware\LogRequest;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/appTopCategory', AppTopCategoryController::class)
    ->middleware(['throttle:5,1', LogRequest::class]);
