<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('home');
Route::view('/accommodation', 'accommodation')->name('accommodation');
Route::view('/offers', 'offers')->name('offers');
Route::view('/gallery', 'gallery')->name('gallery');
Route::view('/dining', 'dining')->name('dining');
Route::view('/events', 'events')->name('events');