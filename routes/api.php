<?php

use Illuminate\Support\Facades\Route;

// ─── Public routes (no auth required) ────────────────────────────────────────

// Auth
Route::prefix('auth')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLink']);
    Route::post('/forgot-password/send-otp',   [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendOtp']);
    Route::post('/forgot-password/verify-otp', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyOtp']);
    Route::post('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset']);
    Route::get('/verify-reset-token', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'verify']);
});

// Projects — public read
Route::get('/projects',      [\App\Http\Controllers\Api\ProjectController::class, 'index']);
Route::get('/projects/{id}', [\App\Http\Controllers\Api\ProjectController::class, 'show']);

// Categories — public read
Route::get('/categories',      [\App\Http\Controllers\Api\CategoryController::class, 'index']);
Route::get('/categories/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'show']);

// Why Us — public read
Route::get('/why-us',       [\App\Http\Controllers\Api\WhyUsController::class, 'index']);
Route::get('/why-us/icons', [\App\Http\Controllers\Api\WhyUsController::class, 'icons']);

// Qui Sommes-Nous — public read
Route::get('/qui-sommes-nous',        [\App\Http\Controllers\Api\QuiSommesNousController::class, 'index']);
Route::get('/qui-sommes-nous/public', [\App\Http\Controllers\Api\QuiSommesNousController::class, 'publicData']);

// Notre Histoire — public read
Route::get('/notre-histoire',        [\App\Http\Controllers\Api\NotreHistoireController::class, 'index']);
Route::get('/notre-histoire/public', [\App\Http\Controllers\Api\NotreHistoireController::class, 'publicData']);

// Services Section — public read
Route::get('/services-section',        [\App\Http\Controllers\Api\ServicesSectionController::class, 'index']);
Route::get('/services-section/public', [\App\Http\Controllers\Api\ServicesSectionController::class, 'publicData']);

// Recruitment page — public read + public application submission
Route::get('/recruitment',        [\App\Http\Controllers\Api\RecruitmentController::class, 'index']);
Route::get('/recruitment/public', [\App\Http\Controllers\Api\RecruitmentController::class, 'publicData']);
Route::post('/candidatures',      [\App\Http\Controllers\Api\CandidatureController::class, 'store'])->middleware('throttle:10,1');

// Contact page — public gallery read + public message submission
Route::get('/contact/universe',   [\App\Http\Controllers\Api\ContactController::class, 'universePublic']);
Route::post('/contact-messages',  [\App\Http\Controllers\Api\ContactController::class, 'storeMessage'])->middleware('throttle:10,1');

// ─── Protected routes (auth:sanctum required) ─────────────────────────────────

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [\App\Http\Controllers\Auth\LoginController::class, 'currentUser']);
    Route::get('/profile',                [\App\Http\Controllers\Api\ProfileController::class, 'show']);
    Route::put('/profile',                [\App\Http\Controllers\Api\ProfileController::class, 'update']);
    Route::post('/profile/change-password',      [\App\Http\Controllers\Api\ProfileController::class, 'changePassword']);
    Route::post('/profile/verify-email-change',  [\App\Http\Controllers\Api\ProfileController::class, 'verifyEmailChange']);
    Route::post('/auth/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);

    // User statistics and recent users (must be before apiResource to avoid {id} conflict)
    Route::get('/users/stats',  [\App\Http\Controllers\Api\UserController::class, 'stats']);
    Route::get('/users/recent', [\App\Http\Controllers\Api\UserController::class, 'recent']);

    // Users CRUD
    Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);

    // Projects CRUD (write operations only — reads are handled by public routes above)
    Route::post('/projects/upload-media', [\App\Http\Controllers\Api\ProjectController::class, 'uploadMedia']);
    Route::post('/projects',              [\App\Http\Controllers\Api\ProjectController::class, 'store']);
    Route::put('/projects/{id}',          [\App\Http\Controllers\Api\ProjectController::class, 'update']);
    Route::patch('/projects/{id}',        [\App\Http\Controllers\Api\ProjectController::class, 'update']);
    Route::delete('/projects/{id}',       [\App\Http\Controllers\Api\ProjectController::class, 'destroy']);

    // Categories CRUD (write operations only — reads are handled by public routes above)
    Route::post('/categories',            [\App\Http\Controllers\Api\CategoryController::class, 'store']);
    Route::put('/categories/{id}',        [\App\Http\Controllers\Api\CategoryController::class, 'update']);
    Route::patch('/categories/{id}',      [\App\Http\Controllers\Api\CategoryController::class, 'update']);
    Route::delete('/categories/{id}',     [\App\Http\Controllers\Api\CategoryController::class, 'destroy']);
    Route::post('/categories/reorder',    [\App\Http\Controllers\Api\CategoryController::class, 'reorder']);

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

    // Qui Sommes-Nous — settings
    Route::patch('/qui-sommes-nous/settings',    [\App\Http\Controllers\Api\QuiSommesNousController::class, 'updateSettings']);
    Route::patch('/qui-sommes-nous/description', [\App\Http\Controllers\Api\QuiSommesNousController::class, 'updateDescription']);

    // Qui Sommes-Nous — services
    Route::post('/qui-sommes-nous/services',              [\App\Http\Controllers\Api\QuiSommesNousController::class, 'storeService']);
    Route::put('/qui-sommes-nous/services/{id}',          [\App\Http\Controllers\Api\QuiSommesNousController::class, 'updateService']);
    Route::patch('/qui-sommes-nous/services/{id}/toggle', [\App\Http\Controllers\Api\QuiSommesNousController::class, 'toggleService']);
    Route::delete('/qui-sommes-nous/services/{id}',       [\App\Http\Controllers\Api\QuiSommesNousController::class, 'destroyService']);
    Route::patch('/qui-sommes-nous/services/reorder',     [\App\Http\Controllers\Api\QuiSommesNousController::class, 'reorderServices']);

    // Notre Histoire — management
    Route::post('/notre-histoire',              [\App\Http\Controllers\Api\NotreHistoireController::class, 'store']);
    Route::put('/notre-histoire/{id}',          [\App\Http\Controllers\Api\NotreHistoireController::class, 'update']);
    Route::patch('/notre-histoire/{id}/toggle', [\App\Http\Controllers\Api\NotreHistoireController::class, 'toggle']);
    Route::delete('/notre-histoire/{id}',       [\App\Http\Controllers\Api\NotreHistoireController::class, 'destroy']);
    Route::patch('/notre-histoire/reorder',     [\App\Http\Controllers\Api\NotreHistoireController::class, 'reorder']);

    // Services Section — settings (intro / approach title / button label)
    Route::patch('/services-section/settings', [\App\Http\Controllers\Api\ServicesSectionController::class, 'updateSettings']);

    // Services Section — approach steps
    Route::post('/services-section/steps',              [\App\Http\Controllers\Api\ServicesSectionController::class, 'storeStep']);
    Route::put('/services-section/steps/{id}',          [\App\Http\Controllers\Api\ServicesSectionController::class, 'updateStep']);
    Route::patch('/services-section/steps/{id}/toggle', [\App\Http\Controllers\Api\ServicesSectionController::class, 'toggleStep']);
    Route::delete('/services-section/steps/{id}',       [\App\Http\Controllers\Api\ServicesSectionController::class, 'destroyStep']);
    Route::patch('/services-section/steps/reorder',     [\App\Http\Controllers\Api\ServicesSectionController::class, 'reorderSteps']);

    // Services Section — service cards
    Route::post('/services-section/cards',              [\App\Http\Controllers\Api\ServicesSectionController::class, 'storeCard']);
    Route::put('/services-section/cards/{id}',          [\App\Http\Controllers\Api\ServicesSectionController::class, 'updateCard']);
    Route::patch('/services-section/cards/{id}/toggle', [\App\Http\Controllers\Api\ServicesSectionController::class, 'toggleCard']);
    Route::delete('/services-section/cards/{id}',       [\App\Http\Controllers\Api\ServicesSectionController::class, 'destroyCard']);
    Route::patch('/services-section/cards/reorder',     [\App\Http\Controllers\Api\ServicesSectionController::class, 'reorderCards']);

    // Recruitment — settings
    Route::patch('/recruitment/settings', [\App\Http\Controllers\Api\RecruitmentController::class, 'updateSettings']);

    // Recruitment — job offers
    Route::post('/recruitment/jobs',              [\App\Http\Controllers\Api\RecruitmentController::class, 'storeJob']);
    Route::put('/recruitment/jobs/{id}',          [\App\Http\Controllers\Api\RecruitmentController::class, 'updateJob']);
    Route::patch('/recruitment/jobs/{id}/toggle', [\App\Http\Controllers\Api\RecruitmentController::class, 'toggleJob']);
    Route::delete('/recruitment/jobs/{id}',       [\App\Http\Controllers\Api\RecruitmentController::class, 'destroyJob']);
    Route::patch('/recruitment/jobs/reorder',     [\App\Http\Controllers\Api\RecruitmentController::class, 'reorderJobs']);

    // Recruitment — talents (stagiaires)
    Route::post('/recruitment/talents/upload-image',    [\App\Http\Controllers\Api\RecruitmentController::class, 'uploadTalentImage']);
    Route::post('/recruitment/talents',                 [\App\Http\Controllers\Api\RecruitmentController::class, 'storeTalent']);
    Route::put('/recruitment/talents/{id}',             [\App\Http\Controllers\Api\RecruitmentController::class, 'updateTalent']);
    Route::patch('/recruitment/talents/{id}/toggle',    [\App\Http\Controllers\Api\RecruitmentController::class, 'toggleTalent']);
    Route::delete('/recruitment/talents/{id}',          [\App\Http\Controllers\Api\RecruitmentController::class, 'destroyTalent']);
    Route::patch('/recruitment/talents/reorder',        [\App\Http\Controllers\Api\RecruitmentController::class, 'reorderTalents']);

    // Recruitment — why cards
    Route::post('/recruitment/whys',              [\App\Http\Controllers\Api\RecruitmentController::class, 'storeWhy']);
    Route::put('/recruitment/whys/{id}',          [\App\Http\Controllers\Api\RecruitmentController::class, 'updateWhy']);
    Route::patch('/recruitment/whys/{id}/toggle', [\App\Http\Controllers\Api\RecruitmentController::class, 'toggleWhy']);
    Route::delete('/recruitment/whys/{id}',       [\App\Http\Controllers\Api\RecruitmentController::class, 'destroyWhy']);
    Route::patch('/recruitment/whys/reorder',     [\App\Http\Controllers\Api\RecruitmentController::class, 'reorderWhys']);

    // Candidatures inbox (read / moderate applications received from the site)
    Route::get('/admin/candidatures',          [\App\Http\Controllers\Api\CandidatureController::class, 'index']);
    Route::get('/admin/candidatures/stats',    [\App\Http\Controllers\Api\CandidatureController::class, 'stats']);
    Route::get('/admin/candidatures/postes',   [\App\Http\Controllers\Api\CandidatureController::class, 'postes']);
    Route::get('/admin/candidatures/{id}',     [\App\Http\Controllers\Api\CandidatureController::class, 'show']);
    Route::patch('/admin/candidatures/{id}/status', [\App\Http\Controllers\Api\CandidatureController::class, 'updateStatus']);
    Route::delete('/admin/candidatures/{id}',  [\App\Http\Controllers\Api\CandidatureController::class, 'destroy']);

    // Notifications — new (unread) website submissions feed the header bell
    Route::get('/admin/notifications',            [\App\Http\Controllers\Api\CandidatureController::class, 'notifications']);
    Route::post('/admin/notifications/read-all',  [\App\Http\Controllers\Api\CandidatureController::class, 'markAllRead']);
    Route::patch('/admin/candidatures/{id}/read', [\App\Http\Controllers\Api\CandidatureController::class, 'markRead']);

    // Contact — messages inbox (received from the public contact form)
    Route::get('/admin/contact/messages',          [\App\Http\Controllers\Api\ContactController::class, 'messages']);
    Route::get('/admin/contact/messages/stats',    [\App\Http\Controllers\Api\ContactController::class, 'messagesStats']);
    Route::get('/admin/contact/messages/unread',   [\App\Http\Controllers\Api\ContactController::class, 'unreadNotifications']);
    Route::post('/admin/contact/messages/read-all', [\App\Http\Controllers\Api\ContactController::class, 'markAllMessagesRead']);
    Route::get('/admin/contact/messages/{id}',     [\App\Http\Controllers\Api\ContactController::class, 'showMessage']);
    Route::patch('/admin/contact/messages/{id}/read', [\App\Http\Controllers\Api\ContactController::class, 'markMessageRead']);
    Route::delete('/admin/contact/messages/{id}',  [\App\Http\Controllers\Api\ContactController::class, 'destroyMessage']);

    // Contact — "L'univers de notre agence" gallery images
    Route::get('/admin/contact/universe',          [\App\Http\Controllers\Api\ContactController::class, 'universeIndex']);
    Route::post('/admin/contact/universe',         [\App\Http\Controllers\Api\ContactController::class, 'universeStore']);
    Route::patch('/admin/contact/universe/reorder', [\App\Http\Controllers\Api\ContactController::class, 'universeReorder']);
    Route::put('/admin/contact/universe/{id}',     [\App\Http\Controllers\Api\ContactController::class, 'universeUpdate']);
    Route::patch('/admin/contact/universe/{id}/toggle', [\App\Http\Controllers\Api\ContactController::class, 'universeToggle']);
    Route::delete('/admin/contact/universe/{id}',  [\App\Http\Controllers\Api\ContactController::class, 'universeDestroy']);
});
