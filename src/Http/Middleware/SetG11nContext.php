<?php

declare(strict_types=1);

namespace Victormgomes\G11n\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Victormgomes\G11n\Facades\G11n;

class SetG11nContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'getPreference')) {
            $preferences = [
                'timezone' => $user->getPreference('timezone'),
                'locale' => $user->getPreference('locale'),
                'currency' => $user->getPreference('currency'),
                'date_format' => $user->getPreference('date_format'),
                'time_format' => $user->getPreference('time_format'),
            ];

            G11n::setContext(array_filter($preferences));
        } else {
            // Fallback to headers
            $locale = $request->getPreferredLanguage();
            if ($locale) {
                G11n::setContext(['locale' => $locale]);
            }
        }

        // Apply locale to app
        App::setLocale(G11n::getLocale());

        return $next($request);
    }
}
