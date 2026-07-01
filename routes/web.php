<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\FileRecordController;
use App\Http\Controllers\FileTransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\PublicFileSearchController;
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
Route::get('/privacy-policy',  fn() => view('pages.privacy'))->name('privacy');
Route::get('/terms',           fn() => view('pages.terms'))->name('terms');
Route::get('/help',            fn() => view('pages.help'))->name('help');

// Public File Search — accessible by anyone without login
Route::get('/public/file-search', [PublicFileSearchController::class, 'index'])->name('public.file.search');
Route::get('/public/file-search/result', [PublicFileSearchController::class, 'search'])->name('public.file.search.result');

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
    Route::get('/notifications',             [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all',   [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read',  [NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::get('/notifications/poll',        [NotificationController::class, 'poll'])->name('notifications.poll');
});

/*
|--------------------------------------------------------------------------
| USER DASHBOARD — role:user only
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'no.cache', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN — can create/manage admins, departments, designations
| CANNOT: create files, create users (only admins)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin', 'no.cache'])->group(function () {
    Route::resource('departments', DepartmentController::class);

    // Super Admin manages ADMINS only (not users)
    Route::resource('users', UserController::class);

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
| ADMIN PANEL — admin + super_admin can view; some actions are role-scoped
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:super_admin,admin', 'no.cache'])
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/poll', [AdminDashboardController::class, 'poll'])->name('dashboard.poll');

        // Admin manages USERS only (not admins) — scoped to own department
        Route::resource('users',        AdminUserController::class);
        Route::resource('designations', AdminDesignationController::class);

        // Files (read-only for admin/super_admin — users create)
        Route::get('/files',                    [AdminFileController::class, 'index'])->name('files');
        Route::get('/files/{uuid}/timeline',    [FileTimelineController::class, 'show'])->name('files.timeline');
        Route::get('/files/{uuid}',             [FileTimelineController::class, 'fileDetails'])->name('files.show');

        // Transfer requests — approve/reject is admin-only
        Route::get('/transfer-requests', [TransferApprovalController::class, 'index'])->name('transfer.requests');

        Route::post('/transfer-requests/{uuid}/approve', [TransferApprovalController::class, 'approve'])
            ->middleware('role:admin')
            ->name('transfer.approve');

        Route::post('/transfer-requests/{uuid}/reject', [TransferApprovalController::class, 'reject'])
            ->middleware('role:admin')
            ->name('transfer.reject');

        // Audit logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.logs');

        // Backup — super_admin only
        Route::middleware('role:super_admin')->group(function () {
            Route::get('/backup',                     [BackupController::class, 'index'])->name('backup.index');
            Route::post('/backup',                    [BackupController::class, 'create'])->name('backup.create');
            Route::get('/backup/{filename}/download', [BackupController::class, 'download'])->name('backup.download');
            Route::delete('/backup/{filename}',       [BackupController::class, 'destroy'])->name('backup.destroy');
        });
    });

require __DIR__ . '/auth.php';
