<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileRecordController;
use App\Http\Controllers\FileTransferController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // FILE SYSTEM (all users)
    Route::resource('files', FileRecordController::class);
    Route::post('/file-transfer', [FileTransferController::class, 'store'])->name('files.transfer');
});

/*
|--------------------------------------------------------------------------
| Super Admin ONLY
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::resource('departments', DepartmentController::class);
    Route::resource('designations', DesignationController::class);
});

/*
|--------------------------------------------------------------------------
| Admin + Super Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin,admin'])->group(function () {
    Route::resource('users', UserController::class);
});


// Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
//     ->name('logout');


Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

require __DIR__ . '/auth.php';
