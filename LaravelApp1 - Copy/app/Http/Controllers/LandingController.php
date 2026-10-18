<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Product;

class LandingController extends Controller
{
    public function index()
    {
        // Featured drinks — first 4 available coffee products
        $featured = Product::with('category')
            ->available()
            ->whereHas('category', fn($q) => $q->where('name', 'Coffee'))
            ->take(4)
            ->get();

        // Pastries — all available pastry products
        $pastries = Product::with('category')
            ->available()
            ->whereHas('category', fn($q) => $q->where('name', 'Pastry'))
            ->take(4)
            ->get();

        // All categories (for a future filter or nav)
        $categories = Category::all();

        // Visible announcements for the customer-facing section
        $announcements = Announcement::visible()
            ->forCustomers()
            ->latest()
            ->take(3)
            ->get();

        // Static business info (could move to a settings table later)
        $about = [
            'address' => 'Pinaod, San Ildefonso, Bulacan',
            'phone'   => '0912 345 6789',
            'hours'   => 'Open Daily · 7:00 AM – 9:00 PM',
            'story'   => 'Issang Macchiatos started as a small neighborhood cart and grew into a cozy café. We roast in small batches and bake fresh every morning.',
        ];

        return view('customer.home', compact(
            'featured',
            'pastries',
            'categories',
            'announcements',
            'about'
        ));
    }
}