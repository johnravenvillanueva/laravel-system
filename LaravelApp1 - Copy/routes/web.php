<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\GenericUser;


$fakeAuth = function (string $default = 'admin') {
    if (!Auth::check()) {
        $role = request()->query('as', $default);

        $user = new class([
            'id'         => 1,
            'first_name' => ucfirst($role),
            'last_name'  => 'Preview',
            'email'      => "{$role}@issang.test",
            'phone'      => '09123456789',
            'address'    => '123 Sample Street, Sample City',
            'avatar'     => null,
            'role'       => $role,
            'is_active'  => 1,
            'last_login' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'password'   => '',
        ]) extends GenericUser {
            public function __get($key)
            {
                return $this->attributes[$key] ?? null;
            }
        };

        Auth::setUser($user);
    }
    return Auth::user();
};

/*
|--------------------------------------------------------------------------
| Public / Customer
|--------------------------------------------------------------------------
*/

Route::get('/', function () use ($fakeAuth) {
    $fakeAuth('customer');
    return view('customer.home', [
        'featured' => [],
        'pastries' => [],
    ]);
})->name('home');

Route::get('/menu', function () use ($fakeAuth) {
    $fakeAuth('customer');
    return view('customer.menu', [
        'products'   => [],
        'categories' => [],
    ]);
})->name('menu');

Route::get('/product/{id}', function ($id) use ($fakeAuth) {
    $fakeAuth('customer');
    return view('customer.product-show', [
        'product' => (object)[
            'id'          => $id,
            'name'        => 'Sample Product',
            'description' => 'Sample description',
            'price'       => 0,
            'category'    => (object)['name' => 'Coffee'],
        ],
    ]);
})->name('customer.product.show');

/*
|--------------------------------------------------------------------------
| Customer area
|--------------------------------------------------------------------------
*/

Route::prefix('customer')->name('customer.')->group(function () use ($fakeAuth) {

    Route::get('/cart', function () use ($fakeAuth) {
        $fakeAuth('customer');
        return view('customer.cart', [
            'cart'     => (object)['items' => collect()],
            'subtotal' => 0,
        ]);
    })->name('cart');

    Route::post('/cart/add/{product}', fn() => back())->name('cart.add');
    Route::put('/cart/update/{item}', fn() => back())->name('cart.update');
    Route::delete('/cart/remove/{item}', fn() => back())->name('cart.remove');

    Route::get('/checkout', function () use ($fakeAuth) {
        $fakeAuth('customer');
        return view('customer.checkout', [
            'cart'     => (object)['items' => collect()],
            'subtotal' => 0,
        ]);
    })->name('checkout');

    Route::post('/checkout', fn() => redirect()->route('customer.orders'))
        ->name('checkout.store');

    Route::get('/orders', function () use ($fakeAuth) {
        $fakeAuth('customer');
        return view('customer.orders', ['orders' => collect()]);
    })->name('orders');

    Route::get('/orders/{id}', function ($id) use ($fakeAuth) {
        $fakeAuth('customer');
        return view('customer.order-show', [
            'order' => (object)[
                'id'             => $id,
                'status'         => 'pending',
                'total_amount'   => 0,
                'pickup_date'    => now()->toDateString(),
                'pickup_time'    => '10:00',
                'payment_method' => 'cash',
                'payment_status' => 'pending',
                'notes'          => '',
                'items'          => collect(),
            ],
        ]);
    })->name('orders.show');

    Route::get('/notifications', function () use ($fakeAuth) {
        $fakeAuth('customer');
        return view('customer.notifications', ['notifications' => collect()]);
    })->name('notifications');

    Route::post('/notifications/read-all', fn() => back())
        ->name('notifications.read-all');

    Route::get('/profile', function () use ($fakeAuth) {
        $user = $fakeAuth('customer');
        return view('customer.profile', ['user' => $user]);
    })->name('profile');

    Route::put('/profile', fn() => back())->name('profile.update');
    Route::put('/profile/password', fn() => back())->name('profile.password');
});

/*
|--------------------------------------------------------------------------
| Auth (preview only)
|--------------------------------------------------------------------------
*/

Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', fn() => redirect('/admin/dashboard'))->name('login.attempt');

Route::get('/register', fn() => view('auth.register'))->name('register');
Route::post('/register', fn() => redirect('/'))->name('register.store');

Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
Route::post('/forgot-password', fn() => back())->name('password.email');

Route::post('/logout', fn() => redirect('/'))->name('logout');

/*
|--------------------------------------------------------------------------
| Admin / Staff area
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () use ($fakeAuth) {

    Route::get('/dashboard', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.dashboard', [
            'todaySales'    => 0,
            'pendingOrders' => 0,
            'productCount'  => 0,
            'lowStockCount' => 0,
            'recentOrders'  => collect(),
            'lowStockItems' => collect(),
        ]);
    })->name('dashboard');

    /* ---------- Users ---------- */
    Route::get('/users', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.users.index', ['users' => collect()]);
    })->name('users.index');

    Route::get('/users/create', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.users.create');
    })->name('users.create');

    Route::post('/users', fn() => back())->name('users.store');

    Route::get('/users/{id}/edit', function ($id) use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.users.edit', [
            'user' => (object)[
                'id'         => $id,
                'first_name' => '',
                'last_name'  => '',
                'email'      => '',
                'phone'      => '',
                'role'       => 'staff',
                'is_active'  => 1,
            ],
        ]);
    })->name('users.edit');

    Route::put('/users/{id}', fn() => back())->name('users.update');
    Route::delete('/users/{id}', fn() => back())->name('users.destroy');

    /* ---------- Products ---------- */
    Route::get('/products', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.products.index', [
            'products'   => collect(),
            'categories' => collect(),
        ]);
    })->name('products.index');

    Route::get('/products/create', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.products.create', ['categories' => collect()]);
    })->name('products.create');

    Route::post('/products', fn() => back())->name('products.store');

    Route::get('/products/{id}/edit', function ($id) use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.products.edit', [
            'product' => (object)[
                'id'           => $id,
                'name'         => '',
                'description'  => '',
                'category_id'  => 1,
                'price'        => 0,
                'is_available' => 1,
            ],
            'categories' => collect(),
        ]);
    })->name('products.edit');

    Route::put('/products/{id}', fn() => back())->name('products.update');
    Route::delete('/products/{id}', fn() => back())->name('products.destroy');

    /* ---------- Categories ---------- */
    Route::get('/categories', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.categories.index', ['categories' => collect()]);
    })->name('categories.index');

    Route::get('/categories/create', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.categories.form');
    })->name('categories.create');

    Route::post('/categories', fn() => back())->name('categories.store');

    Route::get('/categories/{id}/edit', function ($id) use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.categories.form', [
            'category' => (object)[
                'id'          => $id,
                'name'        => '',
                'description' => '',
            ],
        ]);
    })->name('categories.edit');

    Route::put('/categories/{id}', fn() => back())->name('categories.update');
    Route::delete('/categories/{id}', fn() => back())->name('categories.destroy');

    /* ---------- Inventory ---------- */
    Route::get('/inventory', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.inventory.index', ['items' => collect()]);
    })->name('inventory.index');

    Route::get('/inventory/create', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.inventory.create');
    })->name('inventory.create');

    Route::post('/inventory', fn() => back())->name('inventory.store');

    Route::get('/inventory/{id}/edit', function ($id) use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.inventory.create');
    })->name('inventory.edit');

    Route::put('/inventory/{id}', fn() => back())->name('inventory.update');
    Route::delete('/inventory/{id}', fn() => back())->name('inventory.destroy');

    Route::get('/inventory/transactions', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.inventory.transactions', ['transactions' => collect()]);
    })->name('inventory.transactions');

    /* ---------- Sales ---------- */
    Route::get('/sales', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.sales.index', ['sales' => collect()]);
    })->name('sales.index');

    Route::get('/sales/create', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.sales.create', ['products' => collect()]);
    })->name('sales.create');

    Route::post('/sales', fn() => back())->name('sales.store');

    Route::get('/sales/{id}', function ($id) use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.sales.show', [
            'sale' => (object)[
                'id'               => $id,
                'total_amount'     => 0,
                'payment_method'   => 'cash',
                'transaction_type' => 'walk-in',
                'created_at'       => now(),
                'items'            => collect(),
            ],
        ]);
    })->name('sales.show');

    /* ---------- Orders ---------- */
    Route::get('/orders', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.orders.index', ['orders' => collect()]);
    })->name('orders.index');

    Route::get('/orders/{id}', function ($id) use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.orders.show', [
            'order' => (object)[
                'id'             => $id,
                'status'         => 'pending',
                'total_amount'   => 0,
                'pickup_date'    => now()->toDateString(),
                'pickup_time'    => '10:00',
                'payment_method' => 'cash',
                'payment_status' => 'pending',
                'notes'          => '',
                'customer'       => (object)[
                    'first_name' => '',
                    'last_name'  => '',
                    'email'      => '',
                    'phone'      => '',
                ],
                'items' => collect(),
            ],
        ]);
    })->name('orders.show');

    Route::put('/orders/{id}/status', fn() => back())->name('orders.status');

    /* ---------- Announcements ---------- */
    Route::get('/announcements', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.announcements.index', ['announcements' => collect()]);
    })->name('announcements.index');

    Route::get('/announcements/create', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.announcements.form');
    })->name('announcements.create');

    Route::post('/announcements', fn() => back())->name('announcements.store');

    Route::get('/announcements/{id}/edit', function ($id) use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.announcements.form', [
            'announcement' => (object)[
                'id'              => $id,
                'title'           => '',
                'content'         => '',
                'target_audience' => 'all',
                'is_active'       => 1,
                'expires_at'      => null,
            ],
        ]);
    })->name('announcements.edit');

    Route::put('/announcements/{id}', fn() => back())->name('announcements.update');
    Route::delete('/announcements/{id}', fn() => back())->name('announcements.destroy');

    /* ---------- Reports ---------- */
    Route::get('/reports/sales', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.reports.sales', [
            'sales'       => collect(),
            'total'       => 0,
            'count'       => 0,
            'avg'         => 0,
            'chartLabels' => [],
            'chartData'   => [],
        ]);
    })->name('reports.sales');

    Route::get('/reports/inventory', function () use ($fakeAuth) {
        $fakeAuth('admin');
        return view('admin.reports.inventory', [
            'items'      => collect(),
            'totalItems' => 0,
            'lowCount'   => 0,
            'totalValue' => 0,
        ]);
    })->name('reports.inventory');

    /* ---------- Profile ---------- */
    Route::get('/profile', function () use ($fakeAuth) {
        $user = $fakeAuth('admin');
        return view('admin.profile', ['user' => $user]);
    })->name('profile');

    Route::put('/profile', fn() => back())->name('profile.update');
    Route::put('/profile/password', fn() => back())->name('profile.password');
});