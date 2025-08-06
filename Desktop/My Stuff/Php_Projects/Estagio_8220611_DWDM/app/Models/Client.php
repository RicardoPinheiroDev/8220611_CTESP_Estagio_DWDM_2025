<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $phone
 * @property string|null $billing_name
 * @property string|null $billing_address
 * @property string|null $vat_number
 * @property bool $is_active
 * @property array|null $additional_contacts
 * @property string|null $user_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon $email_verified_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\User|null $user
 */
class Client extends Authenticatable implements FilamentUser, \Illuminate\Contracts\Auth\Authenticatable, \Illuminate\Contracts\Auth\Access\Authorizable
{
    use HasFactory, Notifiable, HasUuids, \Illuminate\Foundation\Auth\Access\Authorizable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'billing_name',
        'billing_address',
        'vat_number',
        'is_active',
        'additional_contacts',
        'user_id',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'additional_contacts' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->user_id) {
                $user = \App\Models\User::create([
                    'name' => $model->name,
                    'email' => $model->email,
                    'password' => $model->password,
                    'type' => 'client',
                    'status' => $model->is_active ?? true,
                ]);
                $model->user_id = $user->id;
            }
        });

        static::updating(function ($model) {
            if ($model->user_id) {
                \App\Models\User::where('id', $model->user_id)->update([
                    'name' => $model->name,
                    'email' => $model->email,
                    'password' => $model->password,
                    'status' => $model->is_active ?? true,
                ]);
            }
        });

        static::deleting(function ($model) {
            if ($model->user_id) {
                \App\Models\User::where('id', $model->user_id)->delete();
            } elseif ($model->email) {
                \App\Models\User::where('email', $model->email)->delete();
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $panel->getId() === 'client';
    }
    
    public function getAuthPasswordName()
    {
        return 'password';
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    
    public function getAuthIdentifierName()
    {
        return 'id';
    }
    
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }
    
    public function getAuthPassword()
    {
        return $this->password;
    }
    
    public function getRememberToken()
    {
        return $this->remember_token;
    }
    
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }
    
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    public function hostings()
    {
        return $this->hasMany(Hosting::class);
    }

    public function renewals()
    {
        $domainRenewals = $this->hasManyThrough(
            Renewal::class,
            Domain::class,
            'client_id',
            'renewable_id'
        )->where('renewable_type', Domain::class);
        
        $hostingRenewals = $this->hasManyThrough(
            Renewal::class,
            Hosting::class,
            'client_id',
            'renewable_id'
        )->where('renewable_type', Hosting::class);
        
        
        return $domainRenewals->getQuery()
            ->union($hostingRenewals->getQuery())
            ->orderBy('created_at', 'desc');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->unread();
    }

    public function financialMovements()
    {
        return $this->hasMany(FinancialMovement::class, 'client_id', 'id')
            ->orderBy('created_at', 'desc');
    }
}