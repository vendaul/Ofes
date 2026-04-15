<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (! \Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('choose.role');
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Check if user's role matches
        $roleMap = [
            'admin' => '1',
            'instructor' => '2',
            'student' => '3',
            'super_admin' => '4'  // Added for admin user
        ];
        $storedRole = $roleMap[$role] ?? $role;
        if ($user->user_role !== $storedRole) {
            return redirect()->route('choose.role')->with('error', 'Your role does not have access to this area.');
        }

        return $next($request);
    }
}