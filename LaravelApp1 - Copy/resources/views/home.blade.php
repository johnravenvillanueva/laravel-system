
@extends('layouts.main')

@section('title', 'Place an Order | Issang Machiatos')
@section('hideNav', 'true')

@section('content')
    @vite('resources/css/home.css')

    <div class="order-app">
        <header class="topbar">
            <a class="brand" href="{{ route('home') }}" aria-label="Issang Machiatos home">
                <span class="brand-mark" aria-hidden="true">IM</span>
                <span class="brand-name">Issang Machiatos</span>
            </a>
            <div class="account-actions">
                <a class="profile-link" href="{{ route('home') }}">My Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-button" type="submit">Logout</button>
                </form>
            </div>
        </header>

        <nav class="order-nav" aria-label="Main navigation">
            <a href="{{ route('home') }}">Dashboard</a>
            <a href="#order-form" class="is-active" aria-current="page">Place Orders</a>
            <a href="#order-form">My Orders</a>
        </nav>

        <div class="notification-row">
            <button class="notification-button" type="button" aria-label="Notifications">
                <span class="notification-icon" aria-hidden="true"></span>
            </button>
        </div>

        <main class="order-content">
            <h1 class="visually-hidden">Place an order</h1>

            <div class="order-type-tabs" role="tablist" aria-label="Order type">
                <button class="order-type-tab is-selected" type="button" role="tab" aria-selected="true" data-order-type="lunch">
                    <span class="order-type-icon" aria-hidden="true">🍽️</span>
                    <span>Lunch Meal</span>
                </button>
                <button class="order-type-tab" type="button" role="tab" aria-selected="false" data-order-type="coffee">
                    <span class="order-type-icon" aria-hidden="true">☕</span>
                    <span>Coffee and Pastry</span>
                </button>
            </div>

            <section class="order-form-panel" id="order-form" aria-labelledby="order-heading">
                <form id="order-details-form">
                    <h2 id="order-heading">Lunch Details</h2>

                    <div class="form-field meal-field">
                        <label for="meal-description" id="meal-label">What would you like for lunch?</label>
                        <textarea id="meal-description" name="meal_description" rows="3" placeholder="Describe your lunch (ex. Chicken adobo with extra spice)"></textarea>
                    </div>

                    <fieldset class="pickup-fieldset">
                        <legend>Pick-up Details</legend>
                        <div class="form-field">
                            <label for="pickup-date">Pickup Date <span class="required-mark">*</span></label>
                            <input id="pickup-date" name="pickup_date" type="date" min="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="form-field">
                            <label for="pickup-time">Pickup Time <span class="required-mark">*</span></label>
                            <input id="pickup-time" name="pickup_time" type="time" required>
                        </div>
                    </fieldset>

                    <div class="form-field instructions-field">
                        <label for="special-instructions">Special Instructions <span class="required-mark">*</span></label>
                        <textarea id="special-instructions" name="special_instructions" rows="2" placeholder="Any special instructions for your lunch?" required></textarea>
                    </div>

                    <button class="place-order-button" type="submit">Place Order</button>
                    <p class="form-message" id="form-message" role="status" aria-live="polite"></p>
                </form>
            </section>
        </main>
    </div>

    <script>
        (() => {
            const tabs = document.querySelectorAll('.order-type-tab');
            const heading = document.getElementById('order-heading');
            const mealLabel = document.getElementById('meal-label');
            const mealDescription = document.getElementById('meal-description');
            const specialInstructions = document.getElementById('special-instructions');
            const message = document.getElementById('form-message');
            let selectedOrderType = 'Lunch Meal';

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    const isLunch = tab.dataset.orderType === 'lunch';
                    selectedOrderType = isLunch ? 'Lunch Meal' : 'Coffee and Pastry';
                    heading.textContent = isLunch ? 'Lunch Details' : 'Coffee and Pastry Details';
                    mealLabel.textContent = isLunch ? 'What would you like for lunch?' : 'What would you like?';
                    mealDescription.placeholder = isLunch
                        ? 'Describe your lunch (ex. Chicken adobo with extra spice)'
                        : 'Describe your order (ex. Iced latte and butter croissant)';
                    specialInstructions.placeholder = isLunch
                        ? 'Any special instructions for your lunch?'
                        : 'Any special instructions for your coffee or pastry?';
                    tabs.forEach((item) => {
                        const selected = item === tab;
                        item.classList.toggle('is-selected', selected);
                        item.setAttribute('aria-selected', String(selected));
                    });
                    message.textContent = '';
                });
            });

            document.getElementById('order-details-form').addEventListener('submit', (event) => {
                event.preventDefault();
                message.textContent = `${selectedOrderType} details are ready. Please confirm your order with the counter.`;
            });
        })();
    </script>
@endsection
