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
| AUTH USERS (COMMON)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->role === 'super_admin' || $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('files.index');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('files', FileRecordController::class)->only(['index', 'create', 'store', 'show']);
});


Route::middleware(['auth', 'role:super_admin,admin'])->group(function () {
    Route::resource('designations', DesignationController::class);
});


Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::resource('departments', DepartmentController::class);
    Route::resource('users', UserController::class);
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:super_admin,admin'])
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('users', AdminUserController::class);
        Route::resource('designations', AdminDesignationController::class);

        Route::get('/files', [AdminFileController::class, 'index'])
            ->name('files');

        // ✅ TIMELINE (ONLY ONE ROUTE)
        Route::get('/files/{id}/timeline', [FileTimelineController::class, 'show'])
            ->name('files.timeline');

        Route::get('/files/{id}', [FileTimelineController::class, 'fileDetails'])
            ->name('files.show');

        // ✅ TRANSFER REQUESTS
        Route::get('/transfer-requests', [TransferApprovalController::class, 'index'])
            ->name('transfer.requests');

        Route::post('/transfer-requests/{id}/approve', [TransferApprovalController::class, 'approve'])
            ->name('transfer.approve');

        Route::post('/transfer-requests/{id}/reject', [TransferApprovalController::class, 'reject'])
            ->name('transfer.reject');

        Route::get('/public-files', [PublicFileController::class, 'index'])
            ->name('public-files.index');

        Route::get('/public-files/{id}/download', [PublicFileController::class, 'download'])
            ->name('public-files.download');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])
            ->name('audit.logs');
    });


Route::middleware('auth')->group(function () {

    Route::get(
        '/files/{file}/transfer',
        [FileTransferController::class, 'create']
    )->name('files.transfer.create');

    Route::post(
        '/files/transfer',
        [FileTransferController::class, 'store']
    )->name('files.transfer.store');

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');
});



require __DIR__ . '/auth.php';
