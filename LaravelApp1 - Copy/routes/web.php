<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/home', [HomeController::class, 'home'])->name('home');
Auth::routes();

Route::get('/add', [UserController::class, 'index'])->name('add');
Route::post('/add_user', [UserController::class, 'store'])->name('add_user');