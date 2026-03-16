<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupMemberController;
use App\Http\Controllers\InviteController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups/{group}/members', [GroupMemberController::class, 'store'])->name('groups.members.store');
    Route::resource('groups', GroupController::class)->except(['edit', 'update', 'create']);
});

Route::get('/invites/{token}', [InviteController::class, 'show'])
    ->middleware('signed')
    ->name('invite.accept');

Route::post('/invites/{token}', [InviteController::class, 'accept'])
    ->name('invite.accept.store');

require __DIR__ . '/settings.php';
