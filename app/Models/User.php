<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject as ContractsJWTSubject;

class User extends Authenticatable implements ContractsJWTSubject, HasMedia
{
    use HasFactory, HasRoles, HasUuids, InteractsWithMedia, LogsActivity, Notifiable, SoftDeletes;

    protected $appends = [
        'profile_image',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];


    public function getNeedCreatePasswordAttribute()
    {
        return $this->status == 1;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description']) // Only log these attributes
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['password', 'otp', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])
            ->setDescriptionForEvent(function (string $eventName) {
                $modelName = class_basename($this);

                if ($eventName === 'created') {
                    return "{$modelName} with name '{$this->name}' has been created.";
                } elseif ($eventName === 'updated') {
                    $changes = collect($this->getChanges())
                        ->except(['updated_at', 'password', 'otp', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])
                        ->map(function ($newValue, $key) {
                            $oldValue = $this->getOriginal($key);

                            return "{$key}: '{$oldValue}' to '{$newValue}'";
                        })->implode(', ');

                    return "{$modelName} has been updated. Changes: {$changes}";
                } elseif ($eventName === 'deleted') {
                    return "{$modelName} with name '{$this->name}' has been deleted.";
                }

                return "{$modelName} has been {$eventName}.";
            });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'otp',
        'media',
        'password',
        'verified_at',
        'status',
        'phone',
        'updated_at',
        'deleted_at',
        'otp_sent_at',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'profile_photo_path',
        'need_create_password',
        'email_verified_at',
    ];

    protected $statusMap = [
        0 => 'active',
        1 => 'inactive',
    ];

    public function getStatusAttribute($value)
    {
        return $this->statusMap[$value];
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = array_search($value, $this->statusMap);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAttachmentsAttribute()
    {
        if ($this->getFirstMediaUrl('attachments')) {
            return $this->getMedia('attachments')->map(function ($image) {
                return $image->getUrl();
            });
        }

        return [];
    }

    public function getProfileImageAttribute()
    {
        $image = $this->getMedia('profile_image')->last();
        if (! empty($image)) {
            return $image->getUrl();
        }

        return null;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'username' => $this->username,
            'profile_image' => $this->profile_image,
        ];
    }
}
