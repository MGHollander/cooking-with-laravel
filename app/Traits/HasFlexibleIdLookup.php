<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasFlexibleIdLookup
{
    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if ($field === null) {
            if (Str::isUuid($value)) {
                return $this->where('uuid', $value)->firstOrFail();
            }

            if (is_numeric($value)) {
                return $this->where('id', $value)->firstOrFail();
            }
        }

        return parent::resolveRouteBinding($value, $field);
    }

    /**
     * Find a model by its numeric ID or UUID.
     *
     * @param  mixed  $id
     * @return static|null
     */
    public static function findByFlexibleId($id)
    {
        if (Str::isUuid($id)) {
            return static::where('uuid', $id)->first();
        }

        return static::find($id);
    }
}
