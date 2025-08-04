<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $id
 * @property string $renewable_id
 * @property string $renewable_type
 * @property \Illuminate\Support\Carbon $renewed_at
 * @property float $amount
 * @property string $payment_method
 * @property string|null $receipt_file
 * @property string|null $internal_file
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Model|\Eloquent $renewable
 */
class Renewal extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'renewable_id',
        'renewable_type',
        'renewed_at',
        'amount', 
        'payment_method',
        'receipt_file',
        'internal_file',
    ];

    protected $casts = [
        'renewed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function renewable(): MorphTo 
    {
        return $this->morphTo();
    }
}