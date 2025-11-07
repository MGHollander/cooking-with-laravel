<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->determineLocale($request);

        if ($locale && in_array($locale, config('app.available_locales', ['en', 'nl']))) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    /**
     * Determine the locale based on cookie or domain.
     */
    protected function determineLocale(Request $request): ?string
    {
        // Priority 1: Check for preferred_locale cookie
        $cookieLocale = $request->cookie('preferred_locale');
        if ($cookieLocale && in_array($cookieLocale, config('app.available_locales', ['en', 'nl']))) {
            return $cookieLocale;
        }

        // Priority 2: Check domain mapping
        $host = $request->getHost();
        $domainLocales = config('app.locale_domains', []);

        if (isset($domainLocales[$host])) {
            return $domainLocales[$host];
        }

        // Fallback to default locale
        return config('app.locale', 'nl');
    }
}