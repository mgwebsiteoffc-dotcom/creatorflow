<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Brand\AnalyticsController;
use App\Http\Controllers\Brand\AssignmentController as BrandAssignmentController;
use App\Http\Controllers\Brand\CampaignController;
use App\Http\Controllers\Brand\CreatorMarketplaceController;
use App\Http\Controllers\Brand\CsvImportController;
use App\Http\Controllers\Brand\DashboardController as BrandDashboardController;
use App\Http\Controllers\Brand\OnboardingController as BrandOnboardingController;
use App\Http\Controllers\Brand\ProductController;
use App\Http\Controllers\Creator\AssignmentController as CreatorAssignmentController;
use App\Http\Controllers\Creator\DashboardController as CreatorDashboardController;
use App\Http\Controllers\Creator\EarningsController;
use App\Http\Controllers\Creator\MarketplaceController;
use App\Http\Controllers\Creator\OnboardingController as CreatorOnboardingController;
use App\Http\Controllers\Creator\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Shopify\ShopifyInstallController;
use App\Http\Controllers\Shopify\ShopifyWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome')->name('home');

// Shopify install + OAuth (also reachable while authenticated).
Route::prefix('shopify')->name('shopify.')->group(function () {
    Route::get('/install', [ShopifyInstallController::class, 'install'])->name('install');
    Route::get('/callback', [ShopifyInstallController::class, 'callback'])->name('callback');
});

// Webhooks (no CSRF, HMAC verified in controller).
Route::post('/webhooks/shopify', ShopifyWebhookController::class)->name('webhooks.shopify');

/*
|--------------------------------------------------------------------------
| Guest: auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Creator panel (mobile-first PWA)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('creator')->name('creator.')->group(function () {
    Route::get('/onboarding', [CreatorOnboardingController::class, 'create'])->name('onboarding.create');
    Route::post('/onboarding', [CreatorOnboardingController::class, 'store'])->name('onboarding.store');

    Route::middleware('creator')->group(function () {
        Route::get('/', CreatorDashboardController::class)->name('dashboard');

        Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace');
        Route::get('/marketplace/{campaign}', [MarketplaceController::class, 'show'])->name('marketplace.show');
        Route::post('/marketplace/{campaign}/apply', [MarketplaceController::class, 'apply'])->name('marketplace.apply');

        Route::get('/invitations', [CreatorAssignmentController::class, 'invitations'])->name('invitations');
        Route::post('/invitations/{invitation}/accept', [CreatorAssignmentController::class, 'accept'])->name('invitations.accept');
        Route::post('/invitations/{invitation}/decline', [CreatorAssignmentController::class, 'decline'])->name('invitations.decline');

        Route::get('/assignments', [CreatorAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [CreatorAssignmentController::class, 'show'])->name('assignments.show');
        Route::post('/assignments/{assignment}/contract', [CreatorAssignmentController::class, 'signContract'])->name('assignments.contract');
        Route::post('/assignments/{assignment}/content', [CreatorAssignmentController::class, 'submitContent'])->name('assignments.content');

        Route::get('/earnings', [EarningsController::class, 'index'])->name('earnings.index');

        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/social', [ProfileController::class, 'attachSocial'])->name('profile.social');
    });
});

/*
|--------------------------------------------------------------------------
| Brand panel (mobile-first PWA)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'workspace'])->prefix('brand')->name('brand.')->group(function () {
    Route::get('/', BrandDashboardController::class)->name('dashboard');

    Route::get('/onboarding', [BrandOnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding/product', [BrandOnboardingController::class, 'storeManualProduct'])->name('onboarding.product');
    Route::post('/onboarding/analyze', [BrandOnboardingController::class, 'analyze'])->name('onboarding.analyze');
    Route::post('/onboarding/connect-shopify', [BrandOnboardingController::class, 'connectShopify'])->name('onboarding.shopify');
    Route::post('/onboarding/complete', [BrandOnboardingController::class, 'complete'])->name('onboarding.complete');

    Route::get('/products/import', [CsvImportController::class, 'create'])->name('products.import');
    Route::post('/products/import', [CsvImportController::class, 'store'])->name('products.import.store');
    Route::resource('products', ProductController::class)->except(['edit', 'update', 'destroy']);

    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
    Route::post('/campaigns/{campaign}/launch', [CampaignController::class, 'launch'])->name('campaigns.launch');
    Route::get('/campaigns/{campaign}/matches', [CampaignController::class, 'matches'])->name('campaigns.matches');

    Route::get('/creators', [CreatorMarketplaceController::class, 'index'])->name('creators.index');
    Route::get('/creators/{creator}', [CreatorMarketplaceController::class, 'show'])->name('creators.show');
    Route::post('/creators/invite', [CreatorMarketplaceController::class, 'invite'])->name('creators.invite');

    Route::get('/assignments', [BrandAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{assignment}', [BrandAssignmentController::class, 'show'])->name('assignments.show');
    Route::post('/content/{submission}/approve', [BrandAssignmentController::class, 'approveContent'])->name('content.approve');
    Route::post('/content/{submission}/changes', [BrandAssignmentController::class, 'requestChanges'])->name('content.changes');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
});

/*
|--------------------------------------------------------------------------
| Shared: messaging
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{thread}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{thread}', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/campaigns/{campaign}/chat/{creatorId}', [MessageController::class, 'startWithCreator'])->name('messages.start');
});
