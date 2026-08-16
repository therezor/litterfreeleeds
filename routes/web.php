<?php

use App\Http\Controllers\CommunityPickController;
use App\Http\Controllers\JoinController;
use App\Http\Controllers\SetPasswordController;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/upcoming-picks', [CommunityPickController::class, 'index'])->name('upcoming-picks.index');
Route::get('/upcoming-picks/{communityPick:slug}', [CommunityPickController::class, 'show'])->name('upcoming-picks.show');

Route::get('/join', [JoinController::class, 'create'])->name('join.create');
// Open public POST — rate limited per IP.
Route::post('/join', [JoinController::class, 'store'])->middleware('throttle:10,1')->name('join.store');
Route::get('/join/welcome', [JoinController::class, 'welcome'])->name('join.welcome');

// The last step, reached from the verification link. Guarded by a session
// capability rather than middleware — see SetPasswordController.
Route::get('/join/password', [SetPasswordController::class, 'create'])->name('join.password');
Route::post('/join/password', [SetPasswordController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('join.password.store');

Route::view('/join/verified', 'pages.join-verified')->name('join.verified');

// Signed, but deliberately not `auth` — see VerifyEmailController.
Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::view('/purple-bag-conditions', 'pages.purple-bag-conditions')->name('purple-bag-conditions');
Route::view('/about', 'pages.about');
Route::view('/contact-us', 'pages.contact-us');
Route::view('/privacy-policy', 'pages.privacy-policy');
Route::view('/site-map', 'pages.site-map');

// The panel's own registration page is gone (see AppPanelProvider). This keeps
// year-old links from the hero, footer, contact page and site map working.
Route::redirect('/app/register', '/join', 301);
