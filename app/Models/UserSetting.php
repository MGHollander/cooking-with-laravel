<?php

namespace App\Models;

use App\Traits\HasUuidOrId;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    use HasUuidOrId, HasUuids;

    protected $fillable = [
        'user_id',
        'public_url',
        'default_language',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
