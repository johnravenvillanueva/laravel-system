<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Coffee & Pastry Order</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    </head>
    <body>
        <div class="page-shell">
            <div class="app-card">
                <header class="topbar">
                    <div class="brand">
                        <div class="brand-mark" aria-hidden="true"></div>
                        <div class="brand-copy">Isang Machiatos</div>
                    </div>

                    <nav class="nav-group" aria-label="Main navigation">
                        <a href="#" class="nav-link primary">Dashboard</a>
                        <a href="#" class="nav-link">Place Orders</a>
                        <a href="#" class="nav-link">My Orders</a>
                    </nav>

                    <div class="bell-wrap" aria-label="Notifications">🔔</div>
                </header>

                <main class="content">
                    <div class="section-tabs" aria-label="Category tabs">
                        <button class="tab-btn active" type="button">Lunch Meal</button>
                        <button class="tab-btn" type="button">Coffee and Pastry</button>
                    </div>

                    <section class="menu-block">
                        <h2 class="section-title">Coffee Order</h2>
                        <div class="card-grid" aria-label="Coffee order items">
                            <article class="product-card"><div class="thumb"></div><span class="price-badge">49</span></article>
                            <article class="product-card"><div class="thumb"></div><span class="price-badge">49</span></article>
                            <article class="product-card"><div class="thumb"></div><span class="price-badge">49</span></article>
                            <article class="product-card"><div class="thumb"></div><span class="price-badge">49</span></article>
                            <article class="product-card"><div class="thumb"></div><span class="price-badge">49</span></article>
                            <article class="product-card"><div class="thumb"></div><span class="price-badge">49</span></article>
                            <article class="product-card"><div class="thumb"></div><span class="price-badge">49</span></article>
                            <article class="product-card"><div class="thumb"></div><span class="price-badge">49</span></article>
                        </div>
                    </section>

                    <section class="menu-block">
                        <h2 class="section-title">Pastry Order</h2>
                        <div class="card-grid" aria-label="Pastry order items">
                            <article class="product-card"><div class="thumb"></div><span class="price-badge">49</span></article>
                            <article class="product-card"><div class="thumb"></div><span class="price-badge">49</span></article>
                            <article class="product-card"><div class="thumb"></div><span class="price-badge">49</span></article>
                            <article class="product-card"><div class="thumb"></div><span class="price-badge">49</span></article>
                        </div>
                    </section>

                    <section class="menu-block">
                        <h2 class="section-title">Your Orders</h2>
                        <div class="order-list">
                            <div class="order-row">
                                <span>Strawberry Bliss</span>
                                <strong>PHP 49.00</strong>
                            </div>
                            <div class="order-row">
                                <span>Mango Bites</span>
                                <strong>PHP 49.00</strong>
                            </div>
                        </div>

                        <div class="pickup-box">
                            <div class="field">
                                <label for="pickup-date">Pickup Date *</label>
                                <input id="pickup-date" type="date" value="2026-09-27">
                            </div>

                            <div class="field">
                                <label for="pickup-time">Pickup Time *</label>
                                <input id="pickup-time" type="time" value="00:00">
                            </div>
                        </div>

                        <div class="summary">
                            <p><span>Order Summary</span> <strong>Total: PHP 98.00</strong></p>
                        </div>

                        <button class="place-order" type="button">Place Order</button>
                    </section>
                </main>
            </div>
        </div>
    </body>
</html>
