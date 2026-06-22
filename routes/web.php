<?php

use Illuminate\Support\Facades\Route;

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
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingPageController::class, 'index'])->name('welcome');

Route::post('/public-files', [PublicFileController::class, 'store'])
    ->middleware('throttle:public-upload')
    ->name('public-files.store');

/*
|--------------------------------------------------------------------------
| ALL AUTH ROUTES — no.cache prevents back-button after logout
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'no.cache'])->group(function () {

    Route::get('/dashboard', function () {
        return match(auth()->user()->role) {
            'super_admin' => redirect()->route('super_admin.dashboard'),
            'admin'       => redirect()->route('admin.dashboard'),
            default       => redirect()->route('user.dashboard'),
        };
    })->name('dashboard');

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Files — UUID-based route model binding (FileRecord::getRouteKeyName = 'uuid')
    Route::get('/files',               [FileRecordController::class, 'index'])->name('files.index');
    Route::get('/files/create',        [FileRecordController::class, 'create'])->name('files.create');
    Route::post('/files',              [FileRecordController::class, 'store'])->name('files.store');
    Route::get('/files/{file}',        [FileRecordController::class, 'show'])->name('files.show');

    // Transfer uses UUID
    Route::get('/files/{file}/transfer', [FileTransferController::class, 'create'])->name('files.transfer.create');
    Route::post('/files/transfer',       [FileTransferController::class, 'store'])->name('files.transfer.store');

    // Notifications
    Route::get('/notifications',           [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::get('/notifications/poll',      [NotificationController::class, 'poll'])->name('notifications.poll');
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
| SUPER ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin', 'no.cache'])->group(function () {
    // UUID-based route binding via Department/User model getRouteKeyName
    Route::resource('departments', DepartmentController::class);
    Route::resource('users',       UserController::class);

    Route::get('/super-admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('super_admin.dashboard');
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN + ADMIN SHARED
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin,admin', 'no.cache'])->group(function () {
    Route::resource('designations', DesignationController::class);
});

/*
|--------------------------------------------------------------------------
| ADMIN PANEL
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:super_admin,admin', 'no.cache'])
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users & designations (UUID binding)
        Route::resource('users',        AdminUserController::class);
        Route::resource('designations', AdminDesignationController::class);

        // Files — UUID-based timeline (no numeric ID in URL)
        Route::get('/files',                    [AdminFileController::class, 'index'])->name('files');
        Route::get('/files/{uuid}/timeline',    [FileTimelineController::class, 'show'])->name('files.timeline');
        Route::get('/files/{uuid}',             [FileTimelineController::class, 'fileDetails'])->name('files.show');

        // Transfer requests
        Route::get('/transfer-requests', [TransferApprovalController::class, 'index'])->name('transfer.requests');

        Route::post('/transfer-requests/{id}/approve', [TransferApprovalController::class, 'approve'])
            ->middleware('role:admin')
            ->name('transfer.approve');

        Route::post('/transfer-requests/{id}/reject', [TransferApprovalController::class, 'reject'])
            ->middleware('role:admin')
            ->name('transfer.reject');

        // Public files — signed download
        Route::get('/public-files',               [PublicFileController::class, 'index'])->name('public-files.index');
        Route::get('/public-files/{id}/download', [PublicFileController::class, 'download'])
            ->middleware('signed')
            ->name('public-files.download');

        // Audit logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.logs');
    });

require __DIR__ . '/auth.php';
