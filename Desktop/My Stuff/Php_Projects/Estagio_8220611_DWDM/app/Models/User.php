<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
        'type' => UserType::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
            
            if (!$model->type) {
                $model->type = UserType::CLIENT;
            }
        });
    }

    public function isAdmin(): bool
    {
        return $this->type === UserType::ADMIN;
    }

    public function isEmployee(): bool
    {
        return $this->type === UserType::EMPLOYEE;
    }

    public function isClient(): bool
    {
        return $this->type === UserType::CLIENT;
    }

    public function canAccessAdminPanel(): bool
    {
        if (in_array($this->type, [UserType::ADMIN, UserType::EMPLOYEE])) {
            return true;
        }

        return $this->type === UserType::CLIENT && $this->status;
    }
}