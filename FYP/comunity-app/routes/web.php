<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

use App\Livewire\Home;
use App\Livewire\Chat;

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');
Volt::route('/', Home::class)->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::get("chat",Chat::class)->name('chat');
});

Route::middleware(['auth'])->group(function () {

    // Home page (after login)
    // Route::get('/', function () {
    //     return view('home.userpage');
    // })->name('home');

    // Route::get('/announcement', function () {
    //     return view('home.announcement');
    // })->name('announcement');

    // Route::get('/community', function () {
    //     return view('home.community');
    // })->name('community');

    // Route::get('/messaging', function () {
    //     return view('home.messaging');
    // })->name('messaging');

    // Route::get('/complaint-suggestion', function () {
    //     return view('home.complaint');
    // })->name('complaint');

    // Route::get('/contact', function () {
    //     return view('home.contact');
    // })->name('contact');

    // Route::get('/user', function () {
    //     return view('home.user');
    // })->name('user');

    // // Logout
    // Route::get('/logout', function () {
    //     auth()->logout();
    //     return redirect('/signin');
    // })->name('logout');
});
require __DIR__.'/auth.php';
