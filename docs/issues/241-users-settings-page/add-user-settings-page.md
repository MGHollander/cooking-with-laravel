# Task: Add User Settings Page

## Goal
Add a dedicated settings page for users to manage their `public_url` and `default_language`.

## Implementation Details

### 1. Database
- Create a migration for `user_settings` table:
    - `id`: UUID (PK)
    - `user_id`: UUID (FK to `users.id`, unique, cascade on delete)
    - `public_url`: string(50), nullable, unique
    - `default_language`: string(2), default 'nl' (enum: 'nl', 'en')
    - `timestamps`

### 2. Models
- **UserSetting**:
    - Traits: `HasUuids`, `HasUuidOrId`
    - Relationship: `belongsTo(User)`
- **User**:
    - Relationship: `hasOne(UserSetting)`

### 3. Backend (Laravel)
- **Controller**: `App\Http\Controllers\User\UserSettingsController`
    - `edit`: Show the settings form.
    - `update`: Validate and save settings.
- **Routes**:
    - Add localized routes in `web.php`:
        - `users.settings.edit.[nl|en]`
        - `users.settings.update.[nl|en]`

### 4. Frontend (Inertia/Vue)
- **Page**: `resources/js/Pages/Users/Settings.vue`
    - UI similar to `Users/Edit.vue`.
    - Fields:
        - `public_url` (Text input, max 50 chars)
        - `default_language` (Select: English/Nederlands)
- **Navigation**:
    - Add link to `DefaultLayout.vue` user dropdown.
    - Add link to `nav-list-user.blade.php` component.

### 5. Translations
- Add `nav.settings` to `lang/*/nav.php`.
- Add `users.settings` section to `lang/*/users.php`.

## Verification Plan
- Run migrations.
- Access the settings page via the navigation menu.
- Save valid data and verify it persists in the database.
- Verify validation for `public_url` length and uniqueness.
- Verify validation for `default_language` values.
