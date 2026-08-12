<?php

use Illuminate\Support\Facades\Route;

// Auth routes (public)
Route::prefix('auth')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLink']);
    Route::post('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset']);
    Route::get('/verify-reset-token', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'verify']);
});

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [\App\Http\Controllers\Auth\LoginController::class, 'currentUser']);
    Route::post('/auth/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);

    // User statistics and recent users (must be before apiResource to avoid {id} conflict)
    Route::get('/users/stats', [\App\Http\Controllers\Api\UserController::class, 'stats']);
    Route::get('/users/recent', [\App\Http\Controllers\Api\UserController::class, 'recent']);

    // Users CRUD
    Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);

    // Projects CRUD
    Route::post('/projects/upload-media', [\App\Http\Controllers\Api\ProjectController::class, 'uploadMedia']);
    Route::apiResource('projects', \App\Http\Controllers\Api\ProjectController::class);

    // Categories CRUD
    Route::apiResource('categories', \App\Http\Controllers\Api\CategoryController::class);
    Route::post('/categories/reorder', [\App\Http\Controllers\Api\CategoryController::class, 'reorder']);

    // Why Us — settings
    Route::patch('/why-us/settings', [\App\Http\Controllers\Api\WhyUsController::class, 'updateSettings']);

    // Why Us — features
    Route::post('/why-us/features',              [\App\Http\Controllers\Api\WhyUsController::class, 'storeFeature']);
    Route::put('/why-us/features/{id}',          [\App\Http\Controllers\Api\WhyUsController::class, 'updateFeature']);
    Route::patch('/why-us/features/{id}/toggle', [\App\Http\Controllers\Api\WhyUsController::class, 'toggleFeature']);
    Route::delete('/why-us/features/{id}',       [\App\Http\Controllers\Api\WhyUsController::class, 'destroyFeature']);

    // Why Us — cards
    Route::post('/why-us/cards',        [\App\Http\Controllers\Api\WhyUsController::class, 'storeCard']);
    Route::put('/why-us/cards/{id}',    [\App\Http\Controllers\Api\WhyUsController::class, 'updateCard']);
    Route::delete('/why-us/cards/{id}', [\App\Http\Controllers\Api\WhyUsController::class, 'destroyCard']);
});

// Why Us — public read (no auth required)
Route::get('/why-us',       [\App\Http\Controllers\Api\WhyUsController::class, 'index']);
Route::get('/why-us/icons', [\App\Http\Controllers\Api\WhyUsController::class, 'icons']);

// Categories — public read (no auth required)
Route::get('/categories',       [\App\Http\Controllers\Api\CategoryController::class, 'index']);
Route::get('/categories/{id}',  [\App\Http\Controllers\Api\CategoryController::class, 'show']);

// Projects — public read (no auth required)
Route::get('/projects',     [\App\Http\Controllers\Api\ProjectController::class, 'index']);
Route::get('/projects/{id}',[\App\Http\Controllers\Api\ProjectController::class, 'show']);
