<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/about', function(){
//     return view('about');
// });

Route::view('/','home')->name('home');

Route::view('/about','about')->name('about');

Route::view('/in','dashboard');