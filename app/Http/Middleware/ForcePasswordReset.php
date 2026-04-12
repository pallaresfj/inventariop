<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordReset
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Filament::auth()->user();

        if (! ($user instanceof User) || ! $user->force_password_reset) {
            return $next($request);
        }

        if ($request->routeIs('filament.admin.auth.logout')) {
            return $next($request);
        }

        if ($request->routeIs('filament.admin.auth.password-reset.*')) {
            return $next($request);
        }

        if ($request->routeIs('filament.admin.auth.profile*')) {
            return $next($request);
        }

        if ($request->routeIs('*livewire.update')) {
            $referer = (string) $request->headers->get('referer', '');

            if (str_contains($referer, '/admin/profile')) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'You must update your password before continuing.'], 423);
        }

        return redirect()->route('filament.admin.auth.profile');
    }
}
