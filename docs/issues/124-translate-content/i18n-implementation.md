# Multi-Language Implementation Guide

This document explains how to use the multi-language setup in your application using `laravel-vue-i18n`.

## Overview

The application now uses **Laravel's `lang/` directory as the single source of truth** for all translations. Both Blade views (public website) and Vue.js components (admin pages) read from the same translation files.

### Architecture

```
┌─────────────────────┐
│  lang/en/*.php      │ ← Single Source of Truth
│  lang/nl/*.php      │
└──────────┬──────────┘
           │
           ├─→ Blade views (via Laravel's trans(), __(), @lang)
           │
           └─→ Vue components (via laravel-vue-i18n's $t(), trans())
```

## What Was Implemented

1. ✅ Installed `laravel-vue-i18n` npm package
2. ✅ Configured Vite to process translation files
3. ✅ Set up Vue.js app to use i18n
4. ✅ Shared current locale via Inertia

## Usage in Vue Components

### Template Usage

```vue
<template>
  <!-- Simple translation -->
  <h1>{{ $t('recipes.title') }}</h1>
  
  <!-- Translation with parameters -->
  <p>{{ $t('recipes.servings', { count: 4 }) }}</p>
  
  <!-- Translation from validation file -->
  <p>{{ $t('validation.required') }}</p>
</template>
```

### Script Usage

```vue
<script setup>
import { trans } from 'laravel-vue-i18n';

// Get translation value
const title = trans('recipes.easy');

// With parameters
const message = trans('auth.welcome', { name: 'John' });
</script>
```

## Usage in Blade Views

Blade views continue to use Laravel's built-in translation methods (no changes needed):

```blade
{{-- Simple translation --}}
<h1>{{ __('recipes.title') }}</h1>

{{-- Translation with parameters --}}
<p>@lang('recipes.servings', ['count' => 4])</p>

{{-- Alternative syntax --}}
<p>{{ trans('recipes.easy') }}</p>
```

## Adding New Translations

### For PHP Translation Files

1. Add translations to `lang/en/*.php`:
```php
// lang/en/recipes.php
return [
    'title' => 'Recipes',
    'easy' => 'easy',
    'servings' => ':count servings',
];
```

2. Add Dutch translations to `lang/nl/*.php`:
```php
// lang/nl/recipes.php
return [
    'title' => 'Recepten',
    'easy' => 'makkelijk',
    'servings' => ':count porties',
];
```

3. The translations are automatically available in both Blade and Vue!

### Translation File Structure

Your existing translation files work perfectly:
- `lang/en/auth.php` - Authentication messages
- `lang/en/passwords.php` - Password reset messages
- `lang/en/pagination.php` - Pagination text
- `lang/en/recipes.php` - Recipe-specific translations
- `lang/en/validation.php` - Validation messages

And their Dutch equivalents in `lang/nl/`

## Switching Locales

### In Controllers

```php
// Set locale for current request
app()->setLocale('nl');

// Or use middleware
Route::middleware(['locale:nl'])->group(function () {
    // Routes here
});
```

### Detecting User's Locale

You can detect locale from:
1. URL segment: `example.com/nl/recipes`
2. Session: `session()->get('locale')`
3. User preference: `auth()->user()->locale`
4. Browser headers: `$request->getPreferredLanguage(['en', 'nl'])`

## Current Locale in Vue

The current locale is automatically shared with Vue via Inertia:

```vue
<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const currentLocale = page.props.locale; // 'en' or 'nl'
</script>
```

## Example: Update Recipe Form

Here's how you could update the Recipe Form to use translations:

```vue
<script setup>
// At the top of the file
import { trans } from 'laravel-vue-i18n';
</script>

<template>
  <!-- Instead of hardcoded Dutch text -->
  <Label for="title" :value="$t('recipes.title')" />
  
  <!-- Select dropdown with translations -->
  <select v-model="form.difficulty">
    <option value="easy">{{ $t('recipes.easy') }}</option>
    <option value="average">{{ $t('recipes.average') }}</option>
    <option value="difficult">{{ $t('recipes.difficult') }}</option>
  </select>
</template>
```

## Configuration

### Default Locale

Set in `config/app.php`:
```php
'locale' => 'nl',
'fallback_locale' => 'en',
```

### Available Locales

Consider adding a config for available locales:
```php
// config/app.php
'available_locales' => ['en', 'nl'],
```

## Best Practices

1. **Use namespaced keys**: `recipes.easy` instead of just `easy`
2. **Keep translations flat**: Avoid deep nesting
3. **Use parameters**: For dynamic content like `:count servings`
4. **Stay consistent**: Use the same key format across files
5. **Add context**: Use descriptive key names like `recipes.form.title` vs just `title`

## Domain-Based Locale Switching

The application supports automatic locale switching based on the domain name. This allows different language versions to be served from different domains.

### How It Works

The locale is determined by the domain mapping configured in `config/app.php`:

```php
'locale_domains' => [
    'savedflavors.local.nl' => 'nl',
    'savedflavors.local.com' => 'en',
    'savedflavors.nl' => 'nl',
    'savedflavors.com' => 'en',
],
```

The `LocaleMiddleware` checks the request's host against this mapping and automatically sets the appropriate locale for each request.

### Priority Order

The locale detection follows this priority:

1. **Cookie**: `preferred_locale` cookie (set by user preference)
2. **Domain**: Domain mapping from `locale_domains` config
3. **Fallback**: Default locale from `config('app.locale')`

### Testing on Localhost

To test domain-based locale switching on localhost, you need to set up multiple domains pointing to your local development server.

1. **Edit your hosts file** (`/etc/hosts` on macOS/Linux, `C:\Windows\System32\drivers\etc\hosts` on Windows):

```bash
# Add these lines to your hosts file
127.0.0.1    savedflavors.local.nl
127.0.0.1    savedflavors.local.com
```

2. **Test the domains**:
   - Visit `http://savedflavors.local.nl:81` → Dutch locale
   - Visit `http://savedflavors.local.com:81` → English locale

### Manual Locale Switching

You can also manually switch locales using the `LocaleController` or by setting cookies:

```php
// In a controller or route
app()->setLocale('nl');

// Or via POST request to /locale
POST /locale
Content-Type: application/json
{"locale": "nl"}
```

### Debugging Locale Issues

To debug locale detection:

1. **Check current locale** in any controller or view:
```php
dd(app()->getLocale()); // Current locale
dd(config('app.available_locales')); // Available locales
dd(request()->getHost()); // Current host
```

2. **Check middleware execution** by adding logging to `LocaleMiddleware.php`:
```php
public function handle(Request $request, Closure $next): Response
{
    $locale = $this->determineLocale($request);
    Log::info('Detected locale', ['locale' => $locale, 'host' => $request->getHost()]);
    // ... rest of code
}
```

3. **Clear caches** after configuration changes:
```bash
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
```

### Production Considerations

- Ensure your production domains are properly configured in the `locale_domains` array
- Consider using environment variables for domain configuration:
```php
'locale_domains' => [
    env('DOMAIN_NL', 'savedflavors.nl') => 'nl',
    env('DOMAIN_EN', 'savedflavors.com') => 'en',
],
```
- Set up proper SSL certificates for all domains
- Configure your CDN (if used) to handle multiple domains correctly

## Troubleshooting

### Translations Not Showing

1. Clear cache: `./vendor/bin/sail artisan cache:clear`
2. Rebuild assets: `./vendor/bin/sail npm run build`
3. Check translation file exists in `lang/{locale}/`
4. Verify key syntax: `$t('file.key')` not `$t('file/key')`

### Hot Module Replacement

During development with `npm run dev`, translation changes are automatically picked up!

### Translations Not Working

1. **Check domain configuration**: Ensure the domain is correctly mapped in `locale_domains`
2. **Verify middleware registration**: Confirm `LocaleMiddleware` is registered in `app/Http/Kernel.php`
3. **Clear caches**: Run `./vendor/bin/sail artisan cache:clear`
4. **Check translation files**: Ensure files exist in `lang/{locale}/` directories

### Domain Not Recognized

1. **Check hosts file**: Verify local domains are properly configured
2. **Restart services**: Restart your web server or Docker containers
3. **Check DNS**: For production domains, ensure DNS is properly configured
4. **Browser cache**: Clear browser cache and cookies

### Cookie-Based Switching Not Working

1. **Check cookie settings**: Ensure cookies are enabled in browser
2. **Verify cookie domain**: Cookies may not work across different domains
3. **Check HTTPS**: Some browsers restrict cookies on HTTP localhost

## Resources

- [Laravel Localization](https://laravel.com/docs/11.x/localization)
- [laravel-vue-i18n Documentation](https://github.com/xiCO2k/laravel-vue-i18n)