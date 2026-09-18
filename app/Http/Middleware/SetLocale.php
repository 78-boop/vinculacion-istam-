<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Idiomas soportados por la aplicación.
     */
    protected array $supportedLocales = ['es', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');

        if ($locale && in_array($locale, $this->supportedLocales)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}