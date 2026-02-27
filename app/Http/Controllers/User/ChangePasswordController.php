<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class ChangePasswordController extends Controller
{
    /**
     * Display the password reset view.
     *
     * @return \Inertia\Response
     */
    public function edit(Request $request)
    {
        return Inertia::render('Users/UpdatePassword');
    }

    /**
     * Handle an incoming new password request.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->get('new_password')),
        ]);

        event(new PasswordReset($user));

        Session::flash('success', __('users.flash.password_updated'));

        return Inertia::location(route('users.password.edit.'.app()->getLocale()));
    }
}
