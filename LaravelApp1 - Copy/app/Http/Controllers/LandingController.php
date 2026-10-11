<?php

namespace App\Http\Controllers;

use App\Support\DemoData;

class LandingController extends Controller
{
    public function index()
    {
        return view('customer.home', [
            'featured' => DemoData::featuredProducts(),
            'pastries' => DemoData::pastries(),
            'categories' => DemoData::categories(),
            'about' => [
                'address' => 'Pinaod, San Ildefonso, Bulacan',
                'phone' => '0912 345 6789',
                'hours' => 'Open Daily · 7:00 AM – 9:00 PM',
                'story' => 'Issang Macchiatos started as a small neighborhood cart and grew into a cozy café. We roast in small batches and bake fresh every morning.',
            ],
        ]);
    }
}