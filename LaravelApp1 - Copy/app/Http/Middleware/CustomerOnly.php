<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerOnly
{
    /**
     * Only allow authenticated customers through.
     * Admins/staff get redirected to their dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('customer')->check()) {
            if (Auth::guard('admin')->check()) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'That page is for customers only.');
            }

            return redirect()->route('login')
                ->with('error', 'Please sign in to continue.');
        }

        return $next($request);
    }
}