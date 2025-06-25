<?php

namespace App\Models;

use App\Traits\HasRolesPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SaUser extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRolesPermissions;

    protected $guard_name = 'web';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(
            SaRole::class,
            'sa_model_has_sa_roles',
            'sa_user_id',
            'sa_role_id'
        )->wherePivot('model_name', '=', self::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(
            SaPermission::class,
            'sa_model_has_sa_permissions',
            'sa_user_id',
            'sa_permission_id'
        )->wherePivot('model_name', '=', self::class);
    }
}
