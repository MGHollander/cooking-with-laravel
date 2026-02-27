<?php

namespace App\Models;

use App\Traits\HasUuidOrId;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
    use HasFactory, HasUuidOrId, HasUuids;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
