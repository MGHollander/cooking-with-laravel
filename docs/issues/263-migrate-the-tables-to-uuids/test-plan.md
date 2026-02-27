# Manual Test Plan: UUID Migration (Phase 2)

This plan outlines the manual verification steps required to ensure the system is stable after switching route bindings and internal identifiers to UUIDs.

## 1. Recipe Discovery & Viewing

- [ ] Home Page: Verify recipes are displayed and clicking a card leads to the correct recipe page.
- [ ] Search: Perform a search and verify that results are displayed and links work.
- [ ] Direct URL: Verify that a recipe URL with a public_id (e.g., /recipes/my-cake-abc123def456789) still works.
- [ ] Internal Links: Check that alternate language links (language switcher) on the recipe page work correctly.

## 2. Recipe Management (Authenticated)

- [ ] Create Recipe: Add a new recipe and verify it is successfully saved and redirects to the edit page.
- [ ] Edit Recipe: Modify an existing recipe and verify changes are persisted.
- [ ] Delete Recipe: Delete a recipe and verify it is removed from the list and media is handled correctly.
- [ ] Import Recipe: Import a recipe from a URL and verify that the import log is correctly associated with the new recipe (check import_logs table for recipe_uuid).

## 3. User Management (Admin)

- [ ] User Index: View the list of users and verify that edit/delete buttons work.
- [ ] Edit User: Modify user details (name/email) and verify the update works (checks UUID-based unique validation).
- [ ] Delete User: Delete a user and verify that the related data (recipes) is handled according to the existing logic.
- [ ] Profile Access: Access your own profile via the top-right menu and verify the link uses a UUID.

## 4. System Integrity

- [ ] New Record Generation: Create a new User and Recipe via the UI or Tinker, and verify they automatically receive a UUIDv7.
- [ ] Foreign Key Consistency: Verify that new recipes have the correct user_uuid (pointing to the author's UUID) in the database.

## 5. Backward Compatibility

- [ ] Old Slugs: Test a few existing production URLs (if available) to ensure the public_id parsing still works perfectly.

---

## Phase 2 TODOs

- [ ] Perform full manual sweep of the checklist above.
- [ ] Run automated tests: ./vendor/bin/sail artisan test.
- [ ] Deploy Phase 1 & 2 to production/staging.