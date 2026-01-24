<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'url',
        'source',
        'user_id',
        'recipe_id',
        'parsed_data',
        'credits_used',
    ];

    protected $casts = [
        'parsed_data' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $importLog) {
            if (! $importLog->user_uuid && $importLog->user_id) {
                $importLog->user_uuid = User::query()->whereKey($importLog->user_id)->value('uuid');
            }

            if (! $importLog->recipe_uuid && $importLog->recipe_id) {
                $importLog->recipe_uuid = Recipe::query()->whereKey($importLog->recipe_id)->value('uuid');
            }
        });
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
