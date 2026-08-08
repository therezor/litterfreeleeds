<?php

use App\Http\Controllers\CommunityPickController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/upcoming-picks', [CommunityPickController::class, 'index'])->name('upcoming-picks.index');
Route::get('/upcoming-picks/{communityPick:slug}', [CommunityPickController::class, 'show'])->name('upcoming-picks.show');
Route::view('/about', 'pages.about');
Route::view('/contact-us', 'pages.contact-us');
Route::view('/privacy-policy', 'pages.privacy-policy');
Route::view('/site-map', 'pages.site-map');
