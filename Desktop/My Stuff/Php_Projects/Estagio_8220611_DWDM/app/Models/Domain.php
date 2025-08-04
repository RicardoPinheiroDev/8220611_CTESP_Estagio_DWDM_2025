<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Domain extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    
    /**
     * 
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'name',
        'registered_at',
        'expires_at',
        'registrar_id',
        'is_managed',
        'server_id',
        'status',
        'payment_status',
        'next_renewal_price',
        'notes',
    ];

    /**
     * 
     *
     * @var array<string, string>
     */
    protected $casts = [
        'registered_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_managed' => 'boolean',
        'next_renewal_price' => 'decimal:2',
    ];
    

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }



    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function registrar(): BelongsTo
    {
        return $this->belongsTo(Registrar::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function renewals(): MorphMany
    {
        return $this->morphMany(Renewal::class, 'renewable');
    }

    public function hosting(): HasOne
    {
        return $this->hasOne(Hosting::class);
    }
}
