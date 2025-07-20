<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        if (!auth()->check()) {
            return $next($request);
        }

        
        if (!auth()->user() instanceof \App\Models\User) {
            abort(403, 'Access denied. Admin access only.');
        }

        $user = auth()->user();

        
        if (!$user->canAccessAdminPanel()) {
            
            if ($user->type === UserType::CLIENT) {
                return redirect('/client/login')->with('error', 'You do not have permission to access the admin panel. Please contact an administrator if you need access.');
            }
            
          
            abort(403, 'Access denied. You do not have permission to access the admin panel.');
        }

        return $next($request);
    }
}