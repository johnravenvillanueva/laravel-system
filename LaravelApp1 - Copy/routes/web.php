<?php

use App\Http\Controllers\IssangController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login', ['portal' => 'user']);
});

Route::get('/home', [IssangController::class, 'home'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [IssangController::class, 'userDashboard'])->name('user.dashboard');
    Route::get('/place-order', [IssangController::class, 'createOrder'])->name('user.order.create');
    Route::post('/orders', [IssangController::class, 'storeOrder'])->name('user.orders.store');
    Route::get('/my-orders', [IssangController::class, 'userOrders'])->name('user.orders');
    Route::get('/notifications', [IssangController::class, 'notifications'])->name('notifications.index');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [IssangController::class, 'adminDashboard'])->name('dashboard');
        Route::get('/orders', [IssangController::class, 'adminOrders'])->name('orders');
        Route::patch('/orders/{order}/status', [IssangController::class, 'updateOrderStatus'])->name('orders.status');
        Route::get('/sales', [IssangController::class, 'sales'])->name('sales');
        Route::post('/sales', [IssangController::class, 'storeSale'])->name('sales.store');
        Route::get('/inventory', [IssangController::class, 'inventory'])->name('inventory');
        Route::post('/inventory', [IssangController::class, 'storeProduct'])->name('inventory.store');
        Route::put('/inventory/{product}', [IssangController::class, 'updateProduct'])->name('inventory.update');
        Route::delete('/inventory/{product}', [IssangController::class, 'deleteProduct'])->name('inventory.destroy');
        Route::get('/reports', [IssangController::class, 'reports'])->name('reports');
        Route::get('/reports/export', [IssangController::class, 'exportReports'])->name('reports.export');
    });
});

Route::get('/users', [UserController::class, 'users'])->name('users.index');
Route::get('/add', [UserController::class, 'index'])->name('add');
Route::post('/add_user', [UserController::class, 'store'])->name('add_user');

Route::resource('subjects', SubjectController::class);
Route::post('/subjects/{subject}/assign', [SubjectController::class, 'assignUser'])->name('subjects.assign');

Auth::routes();