<?php

namespace Schrojf\Papers\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AuthorizePapers
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Gate::check('viewPapers', [$request->user($this->guard())])) {
            abort(403);
        }

        return $next($request);
    }

    /**
     * Get the user guard for Laravel Papers.
     */
    public static function guard(): string
    {
        return config('papers.guard') ?? config('auth.defaults.guard');
    }
}
