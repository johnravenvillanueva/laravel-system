<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('home');
});

Route::get('/home',[HomeController::class,'home'])->name('home');
Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/add',[UserController::class,'index'])->name('add');
Route::post('/add_user',[UserController::class,'store'])->name('add_user');