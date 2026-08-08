<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::view('/upcoming-picks', 'pages.upcoming-picks');
Route::view('/about', 'pages.about');
Route::view('/contact-us', 'pages.contact-us');
Route::view('/privacy-policy', 'pages.privacy-policy');
Route::view('/site-map', 'pages.site-map');
