<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait HasUuidOrId
{
    /**
     * Scope a query to find a model by either UUID or numeric ID.
     */
    public function scopeWhereUuidOrId(Builder $query, string|int $value): Builder
    {
        if (Str::isUuid((string) $value)) {
            return $query->where($this->getTable().'.uuid', $value);
        }

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
}
