<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;

// The standard routes
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/pending-articles', [ArticleController::class, 'getPending']);
Route::post('/articles/{id}/update', [ArticleController::class, 'update']);
Route::post('/trigger-scan', [ArticleController::class, 'triggerScan']);    