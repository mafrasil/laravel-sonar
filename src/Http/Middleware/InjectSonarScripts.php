<?php

namespace Mafrasil\LaravelSonar\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;

class InjectSonarScripts
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof Response &&
            ! $request->ajax() &&
            $request->header('X-Inertia') === null &&
            str_contains($response->headers->get('Content-Type', ''), 'text/html')) {

            $content = $response->getContent();
            $pos = strripos($content, '</body>');

            if ($pos !== false) {
                $scripts = Blade::render('@once
                    <script src="'.asset('vendor/laravel-sonar/sonar.js').'" defer></script>
                @endonce');
                $content = substr($content, 0, $pos).$scripts.substr($content, $pos);
                $response->setContent($content);
            }
        }

        return $response;
    }
}
