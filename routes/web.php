<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\AstuceController;
use App\Http\Controllers\PartenaritController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Auth;

// Contrôleurs Admin
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AstuceAdminController;
use App\Http\Controllers\Admin\EpisodeAdminController;
use App\Http\Controllers\Admin\PartenariatAdminController;
use App\Http\Controllers\Admin\NewsletterAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route racine - redirection vers la langue par défaut
Route::get('/', function () {
    $locale = session('locale', config('app.locale'));
    return redirect("/$locale");
});

// Routes multilingues avec préfixe de langue
Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => '[a-zA-Z]{2}'],
    'middleware' => 'setlocale'
], function () {
    
    // Page d'accueil
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/about', [HomeController::class, 'about'])->name('about');
    Route::get('/stats', [HomeController::class, 'stats'])->name('stats');
    Route::get('/search', [HomeController::class, 'search'])->name('search');
    Route::get('/search/suggestions', [HomeController::class, 'searchSuggestions'])->name('search.suggestions');
    
    // Episodes
    Route::group(['prefix' => 'episodes'], function () {
        Route::get('/', [EpisodeController::class, 'index'])->name('episodes.index');
        Route::get('/rss', [EpisodeController::class, 'rss'])->name('episodes.rss');
        Route::get('/archive/{year?}/{month?}', [EpisodeController::class, 'archive'])->name('episodes.archive');
        Route::get('/search', [EpisodeController::class, 'search'])->name('episodes.search');
        Route::get('/playlist/{type?}', [EpisodeController::class, 'playlist'])->name('episodes.playlist');
        Route::get('/load-more', [EpisodeController::class, 'loadMore'])->name('episodes.load-more');
        Route::get('/{slug}', [EpisodeController::class, 'show'])->name('episodes.show');
    });
    
    // Astuces
    Route::group(['prefix' => 'astuces'], function () {
        Route::get('/', [AstuceController::class, 'index'])->name('astuces.index');
        Route::get('/create', [AstuceController::class, 'create'])->name('astuces.create');
        Route::post('/', [AstuceController::class, 'store'])->name('astuces.store');
        Route::get('/success', [AstuceController::class, 'success'])->name('astuces.success');
        Route::get('/track/{id}', [AstuceController::class, 'track'])->name('astuces.track');
        Route::get('/{id}', [AstuceController::class, 'show'])->name('astuces.show');
    });
    
    // Partenariats
    Route::group(['prefix' => 'partenariats'], function () {
        Route::get('/', [PartenaritController::class, 'info'])->name('partenariats.info');
        Route::get('/create', [PartenaritController::class, 'create'])->name('partenariats.create');
        Route::post('/', [PartenaritController::class, 'store'])->name('partenariats.store');
        Route::get('/success', [PartenaritController::class, 'success'])->name('partenariats.success');
        Route::get('/track/{id}', [PartenaritController::class, 'track'])->name('partenariats.track');
    });
    
    // Newsletter
    Route::group(['prefix' => 'newsletter'], function () {
        Route::get('/', [NewsletterController::class, 'create'])->name('newsletter.subscribe');
        Route::post('/', [NewsletterController::class, 'store'])->name('newsletter.store');
        Route::get('/success', [NewsletterController::class, 'success'])->name('newsletter.success');
        Route::get('/confirm/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');
        Route::match(['GET', 'POST'], '/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
        Route::get('/preferences/{token}', [NewsletterController::class, 'preferences'])->name('newsletter.preferences');
        Route::post('/preferences/{token}', [NewsletterController::class, 'updatePreferences'])->name('newsletter.preferences.update');
        Route::post('/quick-subscribe', [NewsletterController::class, 'quickSubscribe'])->name('newsletter.quick-subscribe');
    });
    
    // Contact
    Route::group(['prefix' => 'contact'], function () {
        Route::get('/', [ContactController::class, 'create'])->name('contact.create');
        Route::post('/', [ContactController::class, 'store'])->name('contact.store');
        Route::get('/success', [ContactController::class, 'success'])->name('contact.success');
        Route::post('/validate', [ContactController::class, 'validateForm'])->name('contact.validate');
    });
    
    // Blog
    Route::group(['prefix' => 'blog'], function () {
        Route::get('/', [BlogController::class, 'index'])->name('blog.index');
        Route::get('/rss', [BlogController::class, 'rss'])->name('blog.rss');
        Route::get('/search', [BlogController::class, 'search'])->name('blog.search');
        Route::get('/archive/{year?}/{month?}', [BlogController::class, 'archive'])->name('blog.archive');
        Route::get('/category/{category}', [BlogController::class, 'category'])->name('blog.category');
        Route::get('/tag/{tag}', [BlogController::class, 'tag'])->name('blog.tag');
        Route::get('/load-more', [BlogController::class, 'loadMore'])->name('blog.load-more');
        Route::get('/{slug}', [BlogController::class, 'show'])->name('blog.show');
    });
});

// Routes sans préfixe de langue (API, utilitaires)
Route::group(['prefix' => 'api'], function () {
    Route::post('/language', [HomeController::class, 'changeLanguage'])->name('language.change');
});

// Routes SEO
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
Route::get('/rss.xml', [HomeController::class, 'rss'])->name('rss');

// Route de maintenance
Route::get('/maintenance', [HomeController::class, 'maintenance'])->name('maintenance');

/*
|--------------------------------------------------------------------------
| Routes d'Administration
|--------------------------------------------------------------------------
|
| Routes sécurisées pour l'interface d'administration
|
*/

// Routes d'authentification admin (non protégées)
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('security:login');
    Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AdminAuthController::class, 'sendResetLink'])->name('password.email');
});

// Routes d'administration protégées
Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => ['admin', 'log.admin.actions']
], function () {
    
    // Déconnexion
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/stats', [DashboardController::class, 'getStats'])->name('stats');
    Route::get('/export', [DashboardController::class, 'export'])->name('export');
    Route::post('/cleanup', [DashboardController::class, 'cleanup'])->name('cleanup');
    
    // Gestion des épisodes
    Route::resource('episodes', EpisodeAdminController::class)->middleware('can:admin:episodes.manage');
    Route::post('/episodes/{episode}/publish', [EpisodeAdminController::class, 'publish'])->name('episodes.publish');
    Route::post('/episodes/{episode}/unpublish', [EpisodeAdminController::class, 'unpublish'])->name('episodes.unpublish');
    Route::post('/episodes/bulk-action', [EpisodeAdminController::class, 'bulkAction'])->name('episodes.bulk-action');
    Route::get('/episodes/export', [EpisodeAdminController::class, 'export'])->name('episodes.export');
    
    // Modération des astuces
    Route::resource('astuces', AstuceAdminController::class)->middleware('can:admin:astuces.moderate');
    Route::post('/astuces/{astuce}/approve', [AstuceAdminController::class, 'approve'])->name('astuces.approve');
    Route::post('/astuces/{astuce}/reject', [AstuceAdminController::class, 'reject'])->name('astuces.reject');
    Route::post('/astuces/bulk-action', [AstuceAdminController::class, 'bulkAction'])->name('astuces.bulk-action');
    Route::get('/astuces/export', [AstuceAdminController::class, 'export'])->name('astuces.export');
    
    // Gestion des partenariats
    Route::resource('partenariats', PartenariatAdminController::class)->middleware('can:admin:partenariats.manage');
    Route::post('/partenariats/{partenariat}/approve', [PartenariatAdminController::class, 'approve'])->name('partenariats.approve');
    Route::post('/partenariats/{partenariat}/reject', [PartenariatAdminController::class, 'reject'])->name('partenariats.reject');
            Route::post('/partenariats/bulk-action', [PartenariatAdminController::class, 'bulkAction'])->name('partenariats.bulk-action');
    Route::get('/partenariats/export', [PartenariatAdminController::class, 'export'])->name('partenariats.export');
    
    // Gestion de la newsletter
    Route::resource('newsletter', NewsletterAdminController::class)->middleware('can:admin:newsletter.manage');
    Route::post('/newsletter/{abonne}/activate', [NewsletterAdminController::class, 'activate'])->name('newsletter.activate');
    Route::post('/newsletter/{abonne}/deactivate', [NewsletterAdminController::class, 'deactivate'])->name('newsletter.deactivate');
    Route::post('/newsletter/bulk-action', [NewsletterAdminController::class, 'bulkAction'])->name('newsletter.bulk-action');
    Route::get('/newsletter/export', [NewsletterAdminController::class, 'export'])->name('newsletter.export');
    Route::post('/newsletter/send-campaign', [NewsletterAdminController::class, 'sendCampaign'])->name('newsletter.send-campaign');
    
    // Gestion des utilisateurs
    Route::group(['prefix' => 'users', 'as' => 'users.', 'middleware' => 'can:admin:users.manage'], function () {
        Route::get('/', [AdminController::class, 'users'])->name('index');
        Route::get('/create', [AdminController::class, 'createUser'])->name('create');
        Route::post('/', [AdminController::class, 'storeUser'])->name('store');
        Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('edit');
        Route::put('/{user}', [AdminController::class, 'updateUser'])->name('update');
        Route::delete('/{user}', [AdminController::class, 'destroyUser'])->name('destroy');
        Route::post('/{user}/lock', [AdminController::class, 'lockUser'])->name('lock');
        Route::post('/{user}/unlock', [AdminController::class, 'unlockUser'])->name('unlock');
        Route::post('/bulk-action', [AdminController::class, 'bulkUserAction'])->name('bulk-action');
    });
    
    // Logs et sécurité
    Route::group(['prefix' => 'security', 'as' => 'security.', 'middleware' => 'can:admin:logs.view'], function () {
        Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
        Route::get('/failed-attempts', [AdminController::class, 'failedAttempts'])->name('failed-attempts');
        Route::get('/blocked-ips', [AdminController::class, 'blockedIps'])->name('blocked-ips');
        Route::post('/unblock-ip', [AdminController::class, 'unblockIp'])->name('unblock-ip');
        Route::delete('/logs/{log}', [AdminController::class, 'deleteLog'])->name('logs.delete');
        Route::post('/logs/cleanup', [AdminController::class, 'cleanupLogs'])->name('logs.cleanup');
    });
    
    // Paramètres
    Route::group(['prefix' => 'settings', 'as' => 'settings.', 'middleware' => 'can:admin:settings.manage'], function () {
        Route::get('/', [AdminController::class, 'settings'])->name('index');
        Route::post('/', [AdminController::class, 'updateSettings'])->name('update');
        Route::get('/backup', [AdminController::class, 'backup'])->name('backup');
        Route::post('/backup', [AdminController::class, 'createBackup'])->name('backup.create');
        Route::get('/maintenance', [AdminController::class, 'maintenance'])->name('maintenance');
        Route::post('/maintenance', [AdminController::class, 'toggleMaintenance'])->name('maintenance.toggle');
    });
    
    // API Admin
    Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
        Route::get('/security-status', [AdminAuthController::class, 'checkSecurityStatus'])->name('security.status');
        Route::post('/toggle-2fa', [AdminAuthController::class, 'toggleTwoFactor'])->name('toggle-2fa');
        Route::get('/notifications', [AdminController::class, 'getNotifications'])->name('notifications');
        Route::post('/notifications/{id}/read', [AdminController::class, 'markNotificationRead'])->name('notifications.read');
    });
});


// Middleware de sécurité pour les formulaires publics
Route::group(['middleware' => 'security:contact'], function () {
    // Ces routes sont déjà définies plus haut, ce groupe sert juste à appliquer le middleware
});

Route::group(['middleware' => 'security:newsletter'], function () {
    // Ces routes sont déjà définies plus haut, ce groupe sert juste à appliquer le middleware
});

Route::group(['middleware' => 'security:upload'], function () {
    // Routes d'upload de fichiers avec protection
});
