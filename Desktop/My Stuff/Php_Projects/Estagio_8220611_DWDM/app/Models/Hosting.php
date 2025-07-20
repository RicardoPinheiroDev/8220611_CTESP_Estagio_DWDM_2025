<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property string $id
 * @property string $client_id
 * @property string $account_name
 * @property string $domain_id
 * @property string $plan_id
 * @property string $server_id
 * @property \DateTime $starts_at
 * @property \DateTime $expires_at
 * @property string $status
 * @property string $payment_status
 * @property float $next_renewal_price
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\Domain $domain
 * @property-read \App\Models\HostingPlan $plan
 * @property-read \App\Models\Server $server
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Notification> $notifications
 */
class Hosting extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'client_id',
        'account_name',
        'domain_id',
        'plan_id',
        'server_id',
        'starts_at',
        'expires_at',
        'status',
        'payment_status',
        'next_renewal_price',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'next_renewal_price' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(HostingPlan::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function renewals(): MorphMany
    {
        return $this->morphMany(Renewal::class, 'renewable');
    }
}