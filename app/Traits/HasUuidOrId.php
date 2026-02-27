<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasUuidOrId
{
    /**
     * Scope a query to find a model by either UUID or numeric ID.
     */
    public function scopeWhereUuidOrId(Builder $query, string|int $value): Builder
    {
        return $query->where($this->getTable().'.id', $value);
    }

    /**
     * Find a model by either UUID or numeric ID.
     */
    public static function findByUuidOrId(string|int $value): ?static
    {
        return static::whereUuidOrId($value)->first();
    }

    /**
     * Find a model by either UUID or numeric ID, or fail.
     *
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByUuidOrIdOrFail(string|int $value): static
    {
        return static::whereUuidOrId($value)->firstOrFail();
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->whereUuidOrId($value)->first();
    }
}
