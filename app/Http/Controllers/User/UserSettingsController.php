<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserSettingsController extends Controller
{
    public function edit(Request $request): Response
    {
        $settings = $request->user()->settings()->firstOrCreate([], [
            'default_language' => app()->getLocale(),
        ]);

        return Inertia::render('Users/Settings', [
            'public_url' => $settings->public_url,
            'default_language' => $settings->default_language,
        ]);
    }

    public function update(UserSettingsRequest $request): RedirectResponse
    {
        $settings = $request->user()->settings()->firstOrCreate([], [
            'default_language' => app()->getLocale(),
        ]);

        $settings->update($request->validated());

        return redirect()
            ->route('users.settings.edit.'.app()->getLocale())
            ->with('success', __('users.flash.settings_updated', ['name' => $request->user()->name]));
    }
}
