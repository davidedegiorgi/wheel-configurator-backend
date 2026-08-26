<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WheelCategoryController;
use App\Http\Controllers\WheelHubController;
use App\Http\Controllers\WheelComponentController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\PreviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route per il profilo utente
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ==================== AUTH ROUTES ====================
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::delete('/auth/profile', [AuthController::class, 'deleteAccount']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});

// ==================== PUBLIC ROUTES ====================
// Categorie ruote
Route::get('/wheel-categories', [WheelCategoryController::class, 'index']);
Route::get('/wheel-categories/{id}', [WheelCategoryController::class, 'show']);

// Mozzi
Route::get('/wheel-hubs', [WheelHubController::class, 'index']);
Route::get('/wheel-hubs/{id}', [WheelHubController::class, 'show']);

// Componenti
Route::get('/wheel-components/grouped', [WheelComponentController::class, 'grouped']);
Route::get('/wheel-components', [WheelComponentController::class, 'index']);
Route::get('/wheel-components/{id}', [WheelComponentController::class, 'show']);

// Preview e Immagini
Route::post('/configurations/preview', [PreviewController::class, 'configurationPreview']);
Route::get('/wheel-categories/{id}/with-images', [PreviewController::class, 'wheelCategoryWithImages']);

// ==================== PROTECTED ROUTES (USER) ====================
Route::middleware('auth:sanctum')->group(function () {
    // Configurazioni
    Route::get('/configurations', [ConfigurationController::class, 'index']);
    Route::post('/configurations', [ConfigurationController::class, 'store']);
    Route::get('/configurations/{id}', [ConfigurationController::class, 'show']);
    Route::put('/configurations/{id}', [ConfigurationController::class, 'update']);
    Route::delete('/configurations/{id}', [ConfigurationController::class, 'destroy']);

    // Preventivi
    Route::get('/quotes', [QuoteController::class, 'index']);
    Route::post('/quotes', [QuoteController::class, 'store']);
    Route::get('/quotes/{id}', [QuoteController::class, 'show']);
    Route::put('/quotes/{id}', [QuoteController::class, 'update']);
    Route::delete('/quotes/{id}', [QuoteController::class, 'destroy']);
    Route::post('/quotes/{id}/export', [QuoteController::class, 'export']);
});

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Dashboard
    Route::get('/admin/dashboard/stats', [AdminController::class, 'dashboardStats']);
    Route::get('/admin/users', [AdminController::class, 'getUsers']);
    Route::get('/admin/quotes/report', [AdminController::class, 'getQuotesReport']);
    Route::get('/admin/quotes/by-status', [AdminController::class, 'getQuotesByStatus']);
    Route::get('/admin/wheel-categories/popular', [AdminController::class, 'getPopularWheelCategories']);
    Route::get('/admin/revenue/by-month', [AdminController::class, 'getRevenueByMonth']);
    Route::post('/admin/mail/test', [AdminController::class, 'testMail']);

    // Gestione Catalogo
    Route::post('/wheel-categories', [WheelCategoryController::class, 'store']);
    Route::put('/wheel-categories/{id}', [WheelCategoryController::class, 'update']);
    Route::delete('/wheel-categories/{id}', [WheelCategoryController::class, 'destroy']);

    Route::post('/wheel-hubs', [WheelHubController::class, 'store']);
    Route::put('/wheel-hubs/{id}', [WheelHubController::class, 'update']);
    Route::delete('/wheel-hubs/{id}', [WheelHubController::class, 'destroy']);

    Route::post('/wheel-components', [WheelComponentController::class, 'store']);
    Route::put('/wheel-components/{id}', [WheelComponentController::class, 'update']);
    Route::delete('/wheel-components/{id}', [WheelComponentController::class, 'destroy']);
});
