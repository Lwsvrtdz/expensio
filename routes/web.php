<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupMemberController;
use App\Http\Controllers\InviteController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::post('/expenses', [ExpenseController::class, 'storePersonal'])->name('expenses.store');

    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups/{group}/expenses', [ExpenseController::class, 'storeForGroup'])
        ->name('groups.expenses.store');
    Route::post('/groups/{group}/members', [GroupMemberController::class, 'store'])->name('groups.members.store');
    Route::resource('groups', GroupController::class)->except(['edit', 'update', 'create']);

    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
});

Route::get('/invites/{token}', [InviteController::class, 'show'])
    ->name('invite.accept');

Route::post('/invites/{token}', [InviteController::class, 'accept'])
    ->name('invite.accept.store');

require __DIR__ . '/settings.php';
