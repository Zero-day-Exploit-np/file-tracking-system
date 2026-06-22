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
Route::post('/public-files', [PublicFileController::class, 'store'])->name('public-files.store');

/*
|--------------------------------------------------------------------------
| AUTH — ALL ROLES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Smart redirect — each role goes to their own dashboard
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

    // Transfers (all roles)
    Route::get('/files/{file}/transfer', [FileTransferController::class, 'create'])->name('files.transfer.create');
    Route::post('/files/transfer',       [FileTransferController::class, 'store'])->name('files.transfer.store');

    // Notifications (all roles)
    Route::get('/notifications',         [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all',[NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Notification poll endpoint (returns unread count as JSON)
    Route::get('/notifications/poll', [NotificationController::class, 'poll'])->name('notifications.poll');
});

/*
|--------------------------------------------------------------------------
| USER DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN — SYSTEM-WIDE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::resource('departments', DepartmentController::class);
    Route::resource('users', UserController::class);
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN + ADMIN SHARED
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin,admin'])->group(function () {
    Route::resource('designations', DesignationController::class);
});

/*
|--------------------------------------------------------------------------
| ADMIN PANEL  (prefix /admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:super_admin,admin'])
    ->group(function () {

        // Both super_admin and admin share this dashboard route name
        // Super admin is redirected here from /super-admin/dashboard too
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users & Designations management (scoped by dept for admin)
        Route::resource('users',        AdminUserController::class);
        Route::resource('designations', AdminDesignationController::class);

        // Files
        Route::get('/files',              [AdminFileController::class, 'index'])->name('files');
        Route::get('/files/{id}/timeline',[FileTimelineController::class, 'show'])->name('files.timeline');
        Route::get('/files/{id}',         [FileTimelineController::class, 'fileDetails'])->name('files.show');

        // Transfer requests — list for everyone, approve/reject only for admin
        Route::get('/transfer-requests', [TransferApprovalController::class, 'index'])->name('transfer.requests');

        Route::post('/transfer-requests/{id}/approve', [TransferApprovalController::class, 'approve'])
            ->name('transfer.approve')
            ->middleware('role:admin');   // ← ONLY admin, not super_admin

        Route::post('/transfer-requests/{id}/reject', [TransferApprovalController::class, 'reject'])
            ->name('transfer.reject')
            ->middleware('role:admin');   // ← ONLY admin, not super_admin

        // Public files
        Route::get('/public-files',              [PublicFileController::class, 'index'])->name('public-files.index');
        Route::get('/public-files/{id}/download',[PublicFileController::class, 'download'])->name('public-files.download');

        // Audit logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.logs');
    });

/*
|--------------------------------------------------------------------------
| SUPER ADMIN DEDICATED DASHBOARD (same controller, different route name)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin'])
    ->group(function () {
        Route::get('/super-admin/dashboard', [AdminDashboardController::class, 'index'])
            ->name('super_admin.dashboard');
    });

require __DIR__ . '/auth.php';
