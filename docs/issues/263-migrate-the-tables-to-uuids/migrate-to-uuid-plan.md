# UUID Migration Plan for Cooking with Laravel

Based on my analysis of your codebase, I've developed a comprehensive plan to migrate from numeric IDs to UUIDs. Here's the detailed approach:

## 1. Current State Assessment

Your application currently uses:
- Numeric auto-incrementing IDs for all models
- A `public_id` field in the Recipe model using Nanoid (15-char)
- A hybrid approach where public-facing routes use `public_id` but admin routes use numeric IDs

## 2. Implementation Approach

I recommend using **Laravel's built-in HasVersion7Uuids trait** for all primary models as it's:
- Officially supported by Laravel 11
- **Time-ordered**: Provides better database performance (index locality) than standard UUIDv4
- **Chronological**: Allows for natural sorting by ID and provides insights into record creation time
- Simple to implement and compatible with Laravel's ecosystem

For models where chronological ordering is less critical, standard **HasUuids** (UUIDv4) could be used, but for consistency and performance, **UUIDv7** is the preferred choice for this application.

## 3. Migration Strategy

I recommend a **parallel system approach** that:
1. Keeps existing numeric IDs as-is initially
2. Adds UUID columns to all tables
3. Generates UUIDs for all existing records
4. Updates application code to use UUIDs
5. After validation, removes numeric IDs in a future update

This approach is safer than directly replacing primary keys and minimizes disruption.

## 4. Implementation Plan

### Phase 1: Preparation (Non-Destructive)

1. **Add UUID columns to all tables**
   - Create migrations to add `uuid` columns (not primary keys yet)
   - Set appropriate constraints (UNIQUE)
   - Generate **UUIDv7** for existing records

2. **Update models to generate UUIDs**
   - Add **HasVersion7Uuids** trait to models
   - Update model configuration to generate UUIDs
   - Ensure models can find records by UUID

3. **Create route and controller helpers**
   - Create helper methods to support both ID types
   - Allow fetching models by either UUID or numeric ID
   - Ensure all internal model fetches can use either ID type

### Phase 2: Switch to UUIDs (Progressive)

1. **Update public routes to use UUIDs**
   - Modify route definitions to use UUIDs
   - Update controllers to fetch by UUID
   - Preserve backward compatibility with numeric IDs for API endpoints

2. **Update front-end code**
   - Modify templates to use UUIDs in URLs
   - Update forms to use UUIDs in hidden fields
   - Update JavaScript to handle UUID format

3. **Update admin routes and API endpoints**
   - Convert admin routes to use UUIDs
   - Update any API documentation to reference UUIDs

### Phase 3: Optimization (After Validation)

1. **Switch primary keys to UUIDs**
   - Create migrations to change primary key columns
   - Update foreign key references
   - Remove old numeric ID columns if no longer needed
   - This phase should occur after thorough validation in production

## 5. Model-Specific Approach

### Primary Models:

1. **User**
   - Add `uuid` column, generate **UUIDv7**
   - Update auth system to work with UUIDs
   - Update references in Recipe, ImportLog

2. **Recipe**
   - Add `uuid` column, generate **UUIDv7**
   - Keep existing `public_id` for the slug and use UUID for internal references
   - Update references in RecipeTranslation, ImportLog, Media

3. **RecipeTranslation**
   - Add `uuid` column, generate **UUIDv7**
   - Update relationship to Recipe

### Secondary Models:

1. **ImportLog**
   - Add `uuid` column, generate **UUIDv7** (highly recommended for logs)
   - Update references to User and Recipe

### Consider Skipping:

1. **Tags**
   - As mentioned, you can skip UUID migration for tags if IDs won't be public
   - This reduces complexity of the migration

## 6. Special Considerations

### Database Indexes
- Add proper indexes for UUID columns for performance
- **UUIDv7** is used for chronological ordering and better B-tree index performance

### URL Structure
- Decide on URL format (with or without dashes)
- Update route constraints if needed
- Ensure proper URL generation in views

### API Compatibility
- Consider versioning APIs during transition
- Provide deprecation notices for numeric ID endpoints
- Plan for backward compatibility period

## 7. Testing Strategy

1. **Unit Tests**
   - Update existing tests to use UUIDs
   - Add tests for UUID generation and model lookup

2. **Feature Tests**
   - Test route binding with UUIDs
   - Test form submission and processing
   - Test API endpoints

3. **Performance Tests**
   - Benchmark database performance with UUIDs
   - Test application under load

## 8. Rollback Strategy

Implement safety measures in case of issues:
- Maintain dual ID system during transition
- Create rollback migrations
- Document rollback procedures

## Timeline Estimation

- **Phase 1**: 1-2 weeks - Adding UUID columns and updating models
- **Phase 2**: 1-2 weeks - Updating routes, controllers, and views
- **Phase 3**: 2-4 weeks - Testing, validation, and optimization

## Next Steps

1. Decide on the UUID format and implementation approach
2. Create a test branch to implement and validate Phase 1
3. Review database performance considerations
4. Create a detailed implementation schedule

Would you like me to elaborate on any specific aspect of this plan? Or should we discuss any alternative approaches you're considering?