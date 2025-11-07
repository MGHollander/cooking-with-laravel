<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocaleController extends Controller
{
    /**
     * Update the user's preferred locale.
     */
    public function update(Request $request): RedirectResponse|\Symfony\Component\HttpFoundation\Response 
    {
        $request->validate([
            'locale' => 'required|string|in:en,nl',
        ]);

        $locale = $request->input('locale');
        $cookie = cookie('preferred_locale', $locale, 60 * 24 * 365 * 5, '/', null, false, false, false, 'Lax');

        if ($request->header('X-Inertia')) {
            return Inertia::location(back())->withCookie($cookie);
        }

        return back()->withCookie($cookie);
    }
}
