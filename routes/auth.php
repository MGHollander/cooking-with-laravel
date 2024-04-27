<?php

use App\Http\Controllers\Inertia\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Inertia\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Inertia\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Inertia\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Inertia\Auth\NewPasswordController;
use App\Http\Controllers\Inertia\Auth\PasswordResetLinkController;
use App\Http\Controllers\Inertia\Auth\RegisteredUserController;
use App\Http\Controllers\Inertia\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Route::get('register', [RegisteredUserController::class, 'create'])
    //             ->name('register');

    Route::post('registreren', [RegisteredUserController::class, 'store']);

    Route::get('inloggen', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('inloggen', [AuthenticatedSessionController::class, 'store']);

    Route::get('wachtwoord-vergeten', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('wachtwoord-vergeten', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-wachtwoord/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-wachtwoord', [NewPasswordController::class, 'store'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('email-verifieren', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');

    Route::get('email-verifieren/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verificatie-melding', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('wachtwoord-bevestigen', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('wachtwoord-bevestigen', [ConfirmablePasswordController::class, 'store']);

    Route::post('uitloggen', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
