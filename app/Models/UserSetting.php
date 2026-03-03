<?php

namespace App\Models;

use App\Traits\HasUuidOrId;
use Hidehalo\Nanoid\Client;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UserSetting extends Model
{
    use HasUuidOrId, HasUuids;

    protected $fillable = [
        'user_id',
        'public_url',
        'default_language',
    ];

    protected static function booted(): void
    {
        static::creating(function (UserSetting $userSetting) {
            if (! $userSetting->public_url) {
                $client = new Client;
                $alphabet = '0123456789abcdefghijklmnopqrstuvwxyz';
                $suffix = $client->formattedId($alphabet, 10);

                $name = Str::slug($userSetting->user->name);
                $name = Str::limit($name, 39, '');

                $userSetting->public_url = $name.'-'.$suffix;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
