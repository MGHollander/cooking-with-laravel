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
        $user = $request->user();

        return Inertia::render('Users/Settings', [
            'public_url' => $user->public_url,
            'default_language' => $user->default_language,
            'default_visibility' => $user->default_visibility,
            'languages' => \App\Support\LanguageHelper::getAllLanguagesWithTranslation(),
        ]);
    }

    public function update(UserSettingsRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update($request->validated());

        return redirect()
            ->route('users.settings.edit.'.app()->getLocale())
            ->with('success', __('users.flash.settings_updated', ['name' => $user->name]));
    }
}
