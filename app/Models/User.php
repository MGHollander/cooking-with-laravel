<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Jobs\DeleteUserWithRecipes;
use App\Traits\HasUuidOrId;
use Hidehalo\Nanoid\Client;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuidOrId, HasUuids, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'public_url',
        'default_language',
        'default_visibility',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function booted()
    {
        static::creating(function (User $user) {
            if (! $user->public_url) {
                $client = new Client;
                $alphabet = '0123456789abcdefghijklmnopqrstuvwxyz';
                $suffix = $client->formattedId($alphabet, 10);

                $name = Str::slug($user->name);
                $name = Str::limit($name, 39, '');

                $user->public_url = $name.'-'.$suffix;
            }

            if (! $user->default_language) {
                $user->default_language = app()->getLocale();
            }

            if (! $user->default_visibility) {
                $user->default_visibility = 'private';
            }
        });

        static::deleted(function ($user) {
            if (! $user->isForceDeleting()) {
                DeleteUserWithRecipes::dispatch($user->id);
            }
        });
    }

    public function recipes()
    {
        return $this->hasMany(\App\Models\Recipe::class);
    }
}
