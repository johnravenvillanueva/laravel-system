<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Only allow authenticated admins/staff through.
     * Customers (logged in via the `customer` guard) get redirected to home.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check()) {
            // If the user is logged in as a customer, send them home — not to login
            if (Auth::guard('customer')->check()) {
                return redirect()->route('home')
                    ->with('error', 'You do not have access to the admin panel.');
            }

            // Otherwise, send to login
            return redirect()->route('login')
                ->with('error', 'Please sign in as admin or staff.');
        }

        return $next($request);
    }
}