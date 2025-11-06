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

## Next Steps

1. **Build assets**: Run `./vendor/bin/sail npm run build` or `./vendor/bin/sail npm run dev`
2. **Create translation files**: Add more translation keys as needed
3. **Update Vue components**: Replace hardcoded Dutch text with translation functions
4. **Update Blade views**: Replace hardcoded text with `__()` or `@lang()` helpers
5. **Implement locale switching**: Add a language switcher component
6. **Set default locale**: Configure in `config/app.php`

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

## Troubleshooting

### Translations Not Showing

1. Clear cache: `./vendor/bin/sail artisan cache:clear`
2. Rebuild assets: `./vendor/bin/sail npm run build`
3. Check translation file exists in `lang/{locale}/`
4. Verify key syntax: `$t('file.key')` not `$t('file/key')`

### Hot Module Replacement

During development with `npm run dev`, translation changes are automatically picked up!

## Resources

- [laravel-vue-i18n Documentation](https://github.com/xiCO2k/laravel-vue-i18n)
- [Laravel Localization](https://laravel.com/docs/11.x/localization)
- [Vue I18n Concepts](https://vue-i18n.intlify.dev/)