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
use App\Http\Controllers\Admin\BackupController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingPageController::class, 'index'])->name('welcome');
Route::get('/about',           fn() => redirect('/#about'))->name('about');
Route::get('/features',        fn() => redirect('/#features'))->name('features');
Route::get('/public-upload',   fn() => redirect('/#upload'))->name('public-upload');
Route::get('/privacy-policy',  fn() => view('pages.privacy'))->name('privacy');
Route::get('/terms',           fn() => view('pages.terms'))->name('terms');
Route::get('/help',            fn() => view('pages.help'))->name('help');

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

    Route::get('/profile',         [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',       [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo',  [ProfileController::class, 'uploadPhoto'])->name('profile.photo.upload');
    Route::delete('/profile/photo',[ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
    Route::put('/profile/password',[ProfileController::class, 'changePassword'])->name('profile.password.update');
    Route::delete('/profile',      [ProfileController::class, 'destroy'])->name('profile.destroy');

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
| USER DASHBOARD — role:user only
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'no.cache'])->group(function () {
    Route::get('/user/dashboard', function () {
        // If a non-user somehow hits this, redirect to their dashboard
        $role = auth()->user()->role;
        if ($role !== 'user') {
            return redirect()->route($role === 'super_admin' ? 'super_admin.dashboard' : 'admin.dashboard');
        }
        return app(\App\Http\Controllers\UserDashboardController::class)->index();
    })->name('user.dashboard');
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

        Route::post('/transfer-requests/{uuid}/approve', [TransferApprovalController::class, 'approve'])
            ->middleware('role:admin')
            ->name('transfer.approve');

        Route::post('/transfer-requests/{uuid}/reject', [TransferApprovalController::class, 'reject'])
            ->middleware('role:admin')
            ->name('transfer.reject');

        // Public files — private storage, auth-protected download
        Route::get('/public-files',                [PublicFileController::class, 'index'])->name('public-files.index');
        Route::get('/public-files/{uuid}/download', [PublicFileController::class, 'download'])->name('public-files.download');

        // Audit logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.logs');

        // Backup — super_admin only (route-level enforcement; Laravel 12 removed controller middleware)
        Route::middleware('role:super_admin')->group(function () {
            Route::get('/backup',                     [BackupController::class, 'index'])->name('backup.index');
            Route::post('/backup',                    [BackupController::class, 'create'])->name('backup.create');
            Route::get('/backup/{filename}/download', [BackupController::class, 'download'])->name('backup.download');
            Route::delete('/backup/{filename}',       [BackupController::class, 'destroy'])->name('backup.destroy');
        });
    });

require __DIR__ . '/auth.php';
