<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    /**
     * Update the user's preferred locale.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'locale' => 'required|string|in:en,nl',
        ]);

        $locale = $request->input('locale');

        // Set cookie that lasts forever (5 years in practice)
        return back()->withCookie(
            cookie('preferred_locale', $locale, 60 * 24 * 365 * 5, '/', null, false, false, false, 'Lax')
        );
    }
}