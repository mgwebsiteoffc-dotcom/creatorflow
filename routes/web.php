<?php

use App\Http\Controllers\Admin\BillingController as AdminBillingController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\CreatorController as AdminCreatorController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EscrowController as AdminEscrowController;
use App\Http\Controllers\Admin\HomepageController as AdminHomepageController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\Admin\SeoController as AdminSeoController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WorkspaceController as AdminWorkspaceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Brand\AnalyticsController;
use App\Http\Controllers\Brand\ApplicationController as BrandApplicationController;
use App\Http\Controllers\Brand\AssignmentController as BrandAssignmentController;
use App\Http\Controllers\Brand\BillingController as BrandBillingController;
use App\Http\Controllers\Brand\CampaignController;
use App\Http\Controllers\Brand\CampaignReferenceController;
use App\Http\Controllers\Brand\SettingsController as BrandSettingsController;
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
use App\Http\Controllers\MarketingController;
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

Route::get('/features', [MarketingController::class, 'features'])->name('features');
Route::get('/pricing',  [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/about',    [MarketingController::class, 'about'])->name('about');
Route::get('/contact',  [MarketingController::class, 'contact'])->name('contact');

Route::prefix('tools')->name('tools.')->group(function () {
    Route::get('/',                    [MarketingController::class, 'tools'])->name('index');
    Route::get('/roi-calculator',      [MarketingController::class, 'toolRoiCalculator'])->name('roi');
    Route::get('/creator-rate-calculator', [MarketingController::class, 'toolRateCalculator'])->name('rate');
    Route::get('/brief-generator',     [MarketingController::class, 'toolBriefGenerator'])->name('brief');
    // Server-side AI endpoint (falls back gracefully when AI is offline).
    Route::post('/brief-generator/ai', [MarketingController::class, 'generateBriefApi'])->name('brief.generate')
        ->middleware('throttle:20,1');
});

Route::get('/resources',                   [MarketingController::class, 'resources'])->name('resources');
Route::get('/resources/{category}',        [MarketingController::class, 'resourceCategory'])
    ->whereIn('category', ['playbooks', 'benchmarks', 'templates', 'videos'])
    ->name('resources.category');
Route::get('/resources/item/{slug}',       [MarketingController::class, 'resourceShow'])->name('resources.show');
Route::get('/blog',      [MarketingController::class, 'blogIndex'])->name('blog.index');
Route::get('/blog/{slug}', [MarketingController::class, 'blogShow'])->name('blog.show');

// Legal pages — real pages so footer links stop 404-ing.
Route::prefix('legal')->name('legal.')->group(function () {
    Route::get('/terms',              [\App\Http\Controllers\LegalController::class, 'terms'])->name('terms');
    Route::get('/privacy',            [\App\Http\Controllers\LegalController::class, 'privacy'])->name('privacy');
    Route::get('/refund',             [\App\Http\Controllers\LegalController::class, 'refund'])->name('refund');
    Route::get('/cookies',            [\App\Http\Controllers\LegalController::class, 'cookies'])->name('cookies');
    Route::get('/shipping',           [\App\Http\Controllers\LegalController::class, 'shipping'])->name('shipping');
    Route::get('/content-guidelines', [\App\Http\Controllers\LegalController::class, 'content'])->name('content');
    Route::get('/creator-agreement',  [\App\Http\Controllers\LegalController::class, 'creatorAgreement'])->name('creator-agreement');
});

// Industry & campaign-type landing pages (SEO-friendly)
Route::get('/industry/{slug}',      [MarketingController::class, 'industryShow'])->name('industry.show');
Route::get('/campaign/{slug}',      [MarketingController::class, 'campaignTypeShow'])->name('campaign-type.show');

// Programmatic service + city landing pages (SEO)
Route::get('/services',                     [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}',           [ServiceController::class, 'show'])->name('services.show');
Route::get('/services/{service}/{city}',    [ServiceController::class, 'showInCity'])->name('services.city');

// LLM-friendly answer engines
Route::get('/llms.txt',                     [MarketingController::class, 'llmsTxt'])->name('llms');

// Marketing contact form → leads
Route::post('/contact', [LeadController::class, 'store'])->name('leads.store');

// Well-known SEO
Route::get('/sitemap.xml', [MarketingController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt',  [MarketingController::class, 'robots']);

// Shopify install + OAuth (also reachable while authenticated).
Route::prefix('shopify')->name('shopify.')->group(function () {
    Route::match(['GET','POST'], '/install', [ShopifyInstallController::class, 'install'])->name('install');
    Route::get('/callback', [ShopifyInstallController::class, 'callback'])->name('callback');
});

// Webhooks (no CSRF, HMAC verified in controller).
Route::post('/webhooks/shopify',   ShopifyWebhookController::class)->name('webhooks.shopify');
Route::post('/webhooks/razorpay', \App\Http\Controllers\RazorpayWebhookController::class)->name('webhooks.razorpay');

// PWA push notifications (auth optional so anonymous visitors can subscribe too).
Route::get('/push/vapid-key',       [\App\Http\Controllers\PushController::class, 'vapidKey'])->name('push.vapidKey');
Route::middleware('auth')->group(function () {
    Route::post('/push/subscribe',   [\App\Http\Controllers\PushController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [\App\Http\Controllers\PushController::class, 'unsubscribe'])->name('push.unsubscribe');
});

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

    // Notifications (shared for brand + creator)
    Route::get('/notifications',   [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'open'])->name('notifications.open');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
});

/*
|--------------------------------------------------------------------------
| Admin panel (system owner)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');

    Route::get('/users',   [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}',            [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/suspend',   [AdminUserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/unsuspend', [AdminUserController::class, 'unsuspend'])->name('users.unsuspend');
    Route::post('/users/{user}/make-admin',   [AdminUserController::class, 'makeAdmin'])->name('users.makeAdmin');
    Route::post('/users/{user}/remove-admin', [AdminUserController::class, 'removeAdmin'])->name('users.removeAdmin');

    Route::get('/creators',  [AdminCreatorController::class, 'index'])->name('creators.index');
    Route::get('/creators/import', [AdminCreatorController::class, 'importForm'])->name('creators.import');
    Route::post('/creators/import', [AdminCreatorController::class, 'importStore'])->name('creators.import.store');
    Route::get('/creators/{creator}',            [AdminCreatorController::class, 'show'])->name('creators.show');
    Route::post('/creators/{creator}/suspend',   [AdminCreatorController::class, 'suspend'])->name('creators.suspend');
    Route::post('/creators/{creator}/reinstate', [AdminCreatorController::class, 'reinstate'])->name('creators.reinstate');

    Route::get('/workspaces',  [AdminWorkspaceController::class, 'index'])->name('workspaces.index');
    Route::get('/workspaces/{workspace}',            [AdminWorkspaceController::class, 'show'])->name('workspaces.show');
    Route::post('/workspaces/{workspace}/suspend',   [AdminWorkspaceController::class, 'suspend'])->name('workspaces.suspend');
    Route::post('/workspaces/{workspace}/reinstate', [AdminWorkspaceController::class, 'reinstate'])->name('workspaces.reinstate');

    // Billing / invoices — system-wide
    Route::get('/billing',   [AdminBillingController::class, 'index'])->name('billing.index');

    Route::get('/leads',  [AdminLeadController::class, 'index'])->name('leads.index');
    Route::post('/leads/{lead}', [AdminLeadController::class, 'update'])->name('leads.update');

    Route::get('/settings',  [AdminSettingsController::class, 'edit'])->name('settings');
    Route::post('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

    // AI provider settings (superadmin manages the AI key + driver)
    Route::get('/ai',       [\App\Http\Controllers\Admin\AiSettingsController::class, 'edit'])->name('ai.edit');
    Route::post('/ai',      [\App\Http\Controllers\Admin\AiSettingsController::class, 'update'])->name('ai.update');
    Route::post('/ai/test', [\App\Http\Controllers\Admin\AiSettingsController::class, 'test'])->name('ai.test');

    // Integrations — mail, payments (Razorpay), analytics/SEO scripts, feature flags.
    Route::get('/integrations',                     [\App\Http\Controllers\Admin\IntegrationsController::class, 'edit'])->name('integrations.edit');
    Route::post('/integrations/mail',               [\App\Http\Controllers\Admin\IntegrationsController::class, 'updateMail'])->name('integrations.mail.update');
    Route::post('/integrations/mail/test',          [\App\Http\Controllers\Admin\IntegrationsController::class, 'testMail'])->name('integrations.mail.test');
    Route::post('/integrations/analytics',          [\App\Http\Controllers\Admin\IntegrationsController::class, 'updateAnalytics'])->name('integrations.analytics.update');
    Route::post('/integrations/razorpay',           [\App\Http\Controllers\Admin\IntegrationsController::class, 'updateRazorpay'])->name('integrations.razorpay.update');
    Route::post('/integrations/razorpay/test',      [\App\Http\Controllers\Admin\IntegrationsController::class, 'testRazorpay'])->name('integrations.razorpay.test');
    Route::post('/integrations/vapid',               [\App\Http\Controllers\Admin\IntegrationsController::class, 'updateVapid'])->name('integrations.vapid.update');
    Route::post('/integrations/features',            [\App\Http\Controllers\Admin\IntegrationsController::class, 'updateFeatures'])->name('integrations.features.update');

    Route::get('/seo',       AdminSeoController::class)->name('seo');

    Route::get('/homepage',                    [AdminHomepageController::class, 'index'])->name('homepage');
    Route::post('/homepage',                   [AdminHomepageController::class, 'store'])->name('homepage.store');
    Route::post('/homepage/{item}',            [AdminHomepageController::class, 'update'])->name('homepage.update');
    Route::delete('/homepage/{item}',          [AdminHomepageController::class, 'destroy'])->name('homepage.destroy');

    Route::get('/escrow',   [AdminEscrowController::class, 'index'])->name('escrow.index');
    Route::post('/escrow/hold',    [AdminEscrowController::class, 'hold'])->name('escrow.hold');
    Route::post('/escrow/payouts/{payout}/release', [AdminEscrowController::class, 'release'])->name('escrow.release');
    Route::post('/escrow/refund',  [AdminEscrowController::class, 'refund'])->name('escrow.refund');

    Route::get('/blog',           [AdminBlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/create',    [AdminBlogController::class, 'create'])->name('blog.create');
    Route::post('/blog',          [AdminBlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{post}/edit', [AdminBlogController::class, 'edit'])->name('blog.edit');
    Route::post('/blog/{post}',   [AdminBlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{post}', [AdminBlogController::class, 'destroy'])->name('blog.destroy');
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

        Route::get('/applications', [MarketplaceController::class, 'applications'])->name('applications');
        Route::post('/applications/{application}/withdraw', [MarketplaceController::class, 'withdrawApplication'])->name('applications.withdraw');

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
    Route::post('/campaigns/{campaign}/pause',  [CampaignController::class, 'pause'])->name('campaigns.pause');
    Route::post('/campaigns/{campaign}/resume', [CampaignController::class, 'resume'])->name('campaigns.resume');
    Route::post('/campaigns/{campaign}/end',    [CampaignController::class, 'end'])->name('campaigns.end');
    Route::post('/campaigns/{campaign}/cancel', [CampaignController::class, 'cancel'])->name('campaigns.cancel');
    Route::post('/campaigns/{campaign}/references', [CampaignReferenceController::class, 'store'])->name('campaigns.references.store');
    Route::delete('/campaigns/{campaign}/references/{reference}', [CampaignReferenceController::class, 'destroy'])->name('campaigns.references.destroy');

    // Brand profile / settings / billing
    Route::get('/settings',           [BrandSettingsController::class, 'profile'])->name('settings.profile');
    Route::post('/settings/profile',  [BrandSettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::get('/settings/team',      [BrandSettingsController::class, 'team'])->name('settings.team');

    Route::get('/billing',            [BrandBillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/payments',  [BrandBillingController::class, 'storePayment'])->name('billing.payments.store');

    Route::get('/applications',  [BrandApplicationController::class, 'index'])->name('applications.index');
    Route::post('/applications/{application}/shortlist', [BrandApplicationController::class, 'shortlist'])->name('applications.shortlist');
    Route::post('/applications/{application}/approve',   [BrandApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{application}/reject',    [BrandApplicationController::class, 'reject'])->name('applications.reject');

    Route::get('/creators', [CreatorMarketplaceController::class, 'index'])->name('creators.index');
    Route::get('/creators/{creator}', [CreatorMarketplaceController::class, 'show'])->name('creators.show');
    Route::post('/creators/invite', [CreatorMarketplaceController::class, 'invite'])->name('creators.invite');

    Route::get('/assignments', [BrandAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{assignment}', [BrandAssignmentController::class, 'show'])->name('assignments.show');
    Route::post('/content/{submission}/approve', [BrandAssignmentController::class, 'approveContent'])->name('content.approve');
    Route::post('/content/{submission}/changes', [BrandAssignmentController::class, 'requestChanges'])->name('content.changes');

    // Commerce channels (Shopify + manual) — sync + disconnect
    Route::get('/channels',                        [\App\Http\Controllers\Brand\ChannelController::class, 'index'])->name('channels.index');
    Route::post('/channels/{channel}/sync',        [\App\Http\Controllers\Brand\ChannelController::class, 'sync'])->name('channels.sync');
    Route::post('/channels/{channel}/disconnect',  [\App\Http\Controllers\Brand\ChannelController::class, 'disconnect'])->name('channels.disconnect');

    // Barter / seeding order fulfillment
    Route::get('/orders',                                           [\App\Http\Controllers\Brand\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',                                   [\App\Http\Controllers\Brand\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/shipping',                        [\App\Http\Controllers\Brand\OrderController::class, 'updateShipping'])->name('orders.updateShipping');
    Route::post('/orders/{order}/cancel',                           [\App\Http\Controllers\Brand\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/assignments/{assignment}/create-order',           [\App\Http\Controllers\Brand\OrderController::class, 'createFromAssignment'])->name('orders.createFromAssignment');

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
    Route::get('/messages/{thread}/poll', [MessageController::class, 'poll'])->name('messages.poll'); // 15s live poll
    // Support both POST (form buttons) and GET (direct navigation from emails / notifications).
    Route::match(['GET', 'POST'], '/campaigns/{campaign}/chat/{creatorId}', [MessageController::class, 'startWithCreator'])->name('messages.start');
});
