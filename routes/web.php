<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:admin|klant'])->group(function () {
    Route::get('games', [GameController::class, 'index']);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('games/show/{id}', [GameController::class, 'show']);
    Route::get('games/create', [GameController::class, 'create']);
    Route::post('games/store', [GameController::class, 'store']);
    Route::get('games/edit/{id}', [GameController::class, 'edit']);
    Route::get('games/update/{id}', [GameController::class, 'edit']);
    Route::post('games/update/{id}', [GameController::class, 'update']);
    Route::post('games/destroy/{id}', [GameController::class, 'destroy']);
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('permissions', PermissionController::class)->except('show');
    Route::resource('roles', RoleController::class)->except('show');
    Route::resource('role-permissions', RolePermissionController::class)->except('show');
    Route::resource('user-roles', UserRoleController::class)->except('show');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/geheim', function () {
    return view('geheim');
})->middleware('auth');
