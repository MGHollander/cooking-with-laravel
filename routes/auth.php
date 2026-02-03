<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Route::get('registreren', [RegisteredUserController::class, 'create'])->name('register.nl');
    // Route::get('register', [RegisteredUserController::class, 'create'])->name('register.en');

    Route::post('registreren', [RegisteredUserController::class, 'store']);
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('inloggen', [AuthenticatedSessionController::class, 'create'])
        ->name('login.nl');
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login.en');

    Route::post('inloggen', [AuthenticatedSessionController::class, 'store']);
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('wachtwoord-vergeten', [PasswordResetLinkController::class, 'create'])
        ->name('password.request.nl');
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request.en');

    Route::post('wachtwoord-vergeten', [PasswordResetLinkController::class, 'store'])
        ->name('password.email.nl');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email.en');

    Route::get('reset-wachtwoord/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset.nl');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset.en');

    Route::post('reset-wachtwoord', [NewPasswordController::class, 'store'])
        ->name('password.update.nl');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update.en');
});

Route::middleware('auth')->group(function () {
    Route::get('email-verifieren', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice.nl');
    Route::get('email-verification', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice.en');

    Route::get('email-verifieren/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify.nl');
    Route::get('email-verification/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify.en');

    Route::post('email-verificatie-melding', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send.nl');
    Route::post('email-verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send.en');

    Route::get('wachtwoord-bevestigen', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm.nl');
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm.en');

    Route::post('wachtwoord-bevestigen', [ConfirmablePasswordController::class, 'store']);
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('uitloggen', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout.nl');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout.en');
});
