<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\FileRecordController;
use App\Http\Controllers\FileTransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\PublicFileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserDashboardController;

use App\Http\Controllers\Admin\TransferApprovalController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminFileController;
use App\Http\Controllers\Admin\AdminDesignationController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\FileTimelineController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (no auth required)
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingPageController::class, 'index'])->name('welcome');

// Rate-limited public file upload
Route::post('/public-files', [PublicFileController::class, 'store'])
    ->middleware('throttle:public-upload')
    ->name('public-files.store');

/*
|--------------------------------------------------------------------------
| ALL AUTHENTICATED ROUTES — no.cache applied globally here
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'no.cache'])->group(function () {

    // Role-based dashboard redirect
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        if ($role === 'super_admin') return redirect()->route('super_admin.dashboard');
        if ($role === 'admin')       return redirect()->route('admin.dashboard');
        return redirect()->route('user.dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Files (all roles)
    Route::resource('files', FileRecordController::class)->only(['index', 'create', 'store', 'show']);

    // File transfers (all roles — controller enforces dept scope)
    Route::get('/files/{file}/transfer', [FileTransferController::class, 'create'])->name('files.transfer.create');
    Route::post('/files/transfer',       [FileTransferController::class, 'store'])->name('files.transfer.store');

    // Notifications (all roles)
    Route::get('/notifications',          [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all',[NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::get('/notifications/poll',     [NotificationController::class, 'poll'])->name('notifications.poll');
});

/*
|--------------------------------------------------------------------------
| USER DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:user', 'no.cache'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN — SYSTEM-WIDE MANAGEMENT
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin', 'no.cache'])->group(function () {
    Route::resource('departments', DepartmentController::class);
    Route::resource('users', UserController::class);
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN + ADMIN — SHARED
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin,admin', 'no.cache'])->group(function () {
    Route::resource('designations', DesignationController::class);
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN DEDICATED DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin', 'no.cache'])->group(function () {
    Route::get('/super-admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('super_admin.dashboard');
});

/*
|--------------------------------------------------------------------------
| ADMIN PANEL  (/admin prefix)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:super_admin,admin', 'no.cache'])
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('users',        AdminUserController::class);
        Route::resource('designations', AdminDesignationController::class);

        Route::get('/files',               [AdminFileController::class, 'index'])->name('files');
        Route::get('/files/{id}/timeline', [FileTimelineController::class, 'show'])->name('files.timeline');
        Route::get('/files/{id}',          [FileTimelineController::class, 'fileDetails'])->name('files.show');

        // Transfer requests — view: all; approve/reject: admin only
        Route::get('/transfer-requests', [TransferApprovalController::class, 'index'])->name('transfer.requests');

        Route::post('/transfer-requests/{id}/approve', [TransferApprovalController::class, 'approve'])
            ->middleware('role:admin')
            ->name('transfer.approve');

        Route::post('/transfer-requests/{id}/reject', [TransferApprovalController::class, 'reject'])
            ->middleware('role:admin')
            ->name('transfer.reject');

        Route::get('/public-files',               [PublicFileController::class, 'index'])->name('public-files.index');
        Route::get('/public-files/{id}/download', [PublicFileController::class, 'download'])->name('public-files.download');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.logs');
    });

require __DIR__ . '/auth.php';
