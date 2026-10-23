<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EpaperAdminController;
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

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Protected E-Paper Admin Publishing Suite
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [EpaperAdminController::class, 'dashboard'])->name('dashboard');

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
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])
        ->name('users.index')
        ->middleware('role:super_admin,admin');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])
        ->name('users.store')
        ->middleware('role:super_admin,admin');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])
        ->name('users.update')
        ->middleware('role:super_admin,admin');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('role:super_admin,admin');

    // Audit Logs (Super Admin, Admin)
    Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])
        ->name('audit-logs.index')
        ->middleware('role:super_admin,admin');

    // System Settings (Super Admin, Admin)
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])
        ->name('settings.index')
        ->middleware('role:super_admin,admin');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])
        ->name('settings.update')
        ->middleware('role:super_admin,admin');
});
