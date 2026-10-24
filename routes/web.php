<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EpaperAdminController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\ReadershipApiController;
use App\Http\Controllers\PublicEpaperController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| VSP TIMES Public E-Paper Portal Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicEpaperController::class, 'index'])->name('epaper.home');
Route::get('/edition/{edition}', [PublicEpaperController::class, 'reader'])->name('epaper.edition');
Route::get('/edition/{edition}/page/{pageNumber?}', [PublicEpaperController::class, 'reader'])->name('epaper.reader');
Route::get('/archive', [PublicEpaperController::class, 'archive'])->name('epaper.archive');
Route::get('/edition/{edition}/download', [PublicEpaperController::class, 'downloadPdf'])->name('epaper.download');

// Public & Global Language / Locale Switcher
Route::get('/locale/{lang}', function ($lang) {
    if (in_array($lang, ['te', 'en'], true)) {
        session(['locale' => $lang]);
    }

    return back();
})->name('locale.switch');

// Readership Analytics & Counter APIs
Route::get('/api/readership/today', [ReadershipApiController::class, 'getToday'])->name('api.readership.today');
Route::post('/api/track/event', [ReadershipApiController::class, 'recordEvent'])
    ->middleware('throttle:60,1')
    ->name('api.track.event');

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Protected E-Paper Admin Publishing Suite
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', [EpaperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Editions Management
    Route::get('/editions', [EpaperAdminController::class, 'index'])->name('editions.index');

    // Upload & Create (SuperAdmin, Admin, Editor)
    Route::get('/editions/create', [EpaperAdminController::class, 'create'])
        ->name('editions.create')
        ->middleware('role:super_admin,admin,editor');
    Route::post('/editions', [EpaperAdminController::class, 'store'])
        ->name('editions.store')
        ->middleware('role:super_admin,admin,editor');

    // Visual Editor & Metadata
    Route::get('/editions/{edition}/edit', [EpaperAdminController::class, 'edit'])
        ->name('editions.edit')
        ->middleware('role:super_admin,admin,editor,reviewer');
    Route::put('/editions/{edition}', [EpaperAdminController::class, 'update'])
        ->name('editions.update')
        ->middleware('role:super_admin,admin,editor');

    // Page Reordering
    Route::post('/editions/{edition}/reorder', [EpaperAdminController::class, 'reorderPages'])
        ->name('editions.reorder')
        ->middleware('role:super_admin,admin,editor');

    // Workflow Actions
    Route::post('/editions/{edition}/submit', [EpaperAdminController::class, 'submitApproval'])
        ->name('editions.submit')
        ->middleware('role:super_admin,admin,editor');

    Route::post('/editions/{edition}/approve', [EpaperAdminController::class, 'approve'])
        ->name('editions.approve')
        ->middleware('role:super_admin,admin,reviewer');

    Route::post('/editions/{edition}/reject', [EpaperAdminController::class, 'reject'])
        ->name('editions.reject')
        ->middleware('role:super_admin,admin,reviewer');

    Route::post('/editions/{edition}/publish-now', [EpaperAdminController::class, 'publishNow'])
        ->name('editions.publish')
        ->middleware('role:super_admin,admin');

    Route::post('/editions/{edition}/unpublish', [EpaperAdminController::class, 'unpublish'])
        ->name('editions.unpublish')
        ->middleware('role:super_admin,admin');

    Route::delete('/editions/{edition}', [EpaperAdminController::class, 'destroy'])
        ->name('editions.destroy')
        ->middleware('role:super_admin,admin');

    // Language / Locale Switcher
    Route::get('/locale/{lang}', function ($lang) {
        if (in_array($lang, ['te', 'en'], true)) {
            session(['locale' => $lang]);
        }

        return back();
    })->name('locale.switch');

    // User & Role Management (Super Admin, Admin)
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index')
        ->middleware('role:super_admin,admin');
    Route::post('/users', [UserController::class, 'store'])
        ->name('users.store')
        ->middleware('role:super_admin,admin');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware('role:super_admin,admin');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('role:super_admin,admin');

    // Audit Logs (Super Admin, Admin)
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index')
        ->middleware('role:super_admin,admin');

    // System Settings (Super Admin, Admin)
    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('settings.index')
        ->middleware('role:super_admin,admin');
    Route::post('/settings', [SettingsController::class, 'update'])
        ->name('settings.update')
        ->middleware('role:super_admin,admin');
});
