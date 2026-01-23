<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return view('auth.register');
});

Route::get('/pricing', function () {
    return view('welcome');
});

Route::get('/about', function () {
    return view('welcome');
});

Route::get('/history', function () {
    return view('welcome');
});

Route::get('/app', function () {
    return file_get_contents(public_path('index.html'));
});

Route::get('/admin', function () {
    return file_get_contents(public_path('admin.html'));
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
