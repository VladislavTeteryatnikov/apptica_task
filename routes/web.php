<?php

use App\Http\Controllers\AppTopCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/appTopCategory', AppTopCategoryController::class);
