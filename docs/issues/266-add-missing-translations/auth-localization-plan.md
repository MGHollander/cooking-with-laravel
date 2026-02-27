# Auth Route Localization Plan

## 1. Current State Analysis

### Current Auth Routes (routes/auth.php)

The authentication routes are currently defined with Dutch URLs only:

**Guest Routes (middleware: guest):**
- `POST registreren` - User registration (no GET route, commented out)
- `GET/POST inloggen` - Login (named: `login`)
- `GET/POST wachtwoord-vergeten` - Forgot password (named: `password.request`, `password.email`)
- `GET reset-wachtwoord/{token}` - Reset password form (named: `password.reset`)
- `POST reset-wachtwoord` - Reset password submit (named: `password.update`)

**Auth Routes (middleware: auth):**
- `GET email-verifieren` - Email verification prompt (named: `verification.notice`)
- `GET email-verifieren/{id}/{hash}` - Verify email (named: `verification.verify`)
- `POST email/verificatie-melding` - Resend verification (named: `verification.send`)
- `GET/POST wachtwoord-bevestigen` - Confirm password (named: `password.confirm`)
- `POST uitloggen` - Logout (named: `logout`)

### Existing Pattern from web.php

The application already uses a localization pattern for recipe routes:
- Dutch: `/recepten/{slug}` → `recipes.show.nl`
- English: `/recipes/{slug}` → `recipes.show.en`

## 2. Proposed Changes

### Approach

Follow the existing pattern from `web.php` where:
- Dutch routes use `.nl` suffix (e.g., `login.nl`)
- English routes use `.en` suffix (e.g., `login.en`)
- Both language routes point to the same controller methods
- Named routes allow the application to generate URLs in the correct language

### Key Considerations

1. **Named Routes**: Laravel's auth system relies on specific named routes (e.g., `login`, `password.reset`). We need to maintain these base names while adding language-specific alternatives.
2. **Redirects**: Consider adding redirects from one language to the other based on user preference.
3. **Middleware**: Both language versions should use the same middleware groups.

## 3. Route Mapping Table

| Dutch URL | English URL | Route Name (NL) | Route Name (EN) | Controller |
|-----------|-------------|-----------------|-----------------|------------|
| `registreren` | `register` | `register.nl` | `register.en` | `RegisteredUserController` |
| `inloggen` | `login` | `login.nl` | `login.en` | `AuthenticatedSessionController` |
| `wachtwoord-vergeten` | `forgot-password` | `password.request.nl` | `password.request.en` | `PasswordResetLinkController` |
| `reset-wachtwoord/{token}` | `reset-password/{token}` | `password.reset.nl` | `password.reset.en` | `NewPasswordController` |
| `email-verifieren` | `email-verify` | `verification.notice.nl` | `verification.notice.en` | `EmailVerificationPromptController` |
| `email-verifieren/{id}/{hash}` | `email-verify/{id}/{hash}` | `verification.verify.nl` | `verification.verify.en` | `VerifyEmailController` |
| `email/verificatie-melding` | `email/verification-notification` | `verification.send.nl` | `verification.send.en` | `EmailVerificationNotificationController` |
| `wachtwoord-bevestigen` | `confirm-password` | `password.confirm.nl` | `password.confirm.en` | `ConfirmablePasswordController` |
| `uitloggen` | `logout` | `logout.nl` | `logout.en` | `AuthenticatedSessionController` |

## 4. Implementation Steps

### Step 1: Update routes/auth.php

Add English route alternatives alongside existing Dutch routes:

```php
// Guest routes
Route::middleware('guest')->group(function () {
    // Register
    Route::get('registreren', [RegisteredUserController::class, 'create'])->name('register.nl');
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register.en');
    Route::post('registreren', [RegisteredUserController::class, 'store']);
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login
    Route::get('inloggen', [AuthenticatedSessionController::class, 'create'])->name('login.nl');
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login.en');
    Route::post('inloggen', [AuthenticatedSessionController::class, 'store']);
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // ... continue for all routes
});
```

### Step 2: Handle Default Named Routes

Laravel's auth system expects certain named routes (e.g., `login` for redirects). Create a helper or middleware to resolve the correct language-specific route:

**Option A**: Keep base names pointing to Dutch (current behavior) and add `.en` alternatives
**Option B**: Create a route helper that resolves based on current locale

### Step 3: Update Views/Components

Update any hardcoded route references in Vue components and Blade templates to use the localized route names:

```php
// Before
route('login')

// After
route('login.' . app()->getLocale())
```

```vue
// Before
route('login')

// After
const attrs = useAttrs()
route(`login.${attrs.locale}`)
```


### Step 4: Update Email Templates

Password reset and email verification emails contain links. These need to generate URLs in the user's preferred language.

### Step 5: Testing

Create feature tests to verify:
- Both Dutch and English routes are accessible
- Authentication flows work in both languages
- Redirects work correctly after login/logout
- Email links use correct language URLs

### Step 6: Documentation

Update any relevant documentation about the routing structure.

## 5. Files to Modify

1. `routes/auth.php` - Add English route alternatives
2. `resources/js/Pages/Auth/*.vue` - Update form actions if needed
3. `app/Http/Controllers/Auth/*` - May need updates for redirect URLs
4. `resources/views/` - Update any Blade templates with auth links
5. Email templates (if customized)

## 6. Risks and Considerations

1. **Breaking Changes**: Existing bookmarks/links to Dutch URLs should continue to work
2. **SEO**: Consider canonical URLs if both language versions are indexed
3. **Session Handling**: Ensure session/authentication state persists across language switches
4. **Laravel Breeze**: The auth scaffolding may have assumptions about route names
