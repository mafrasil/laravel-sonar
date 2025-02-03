<?php

namespace Mafrasil\LaravelSonar\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuthorizeSonar
{
    public function handle(Request $request, Closure $next)
    {
        return app()->environment('local') ||
        Gate::check('viewSonar', [$request->user()])
        ? $next($request)
        : abort(403);
    }
}
