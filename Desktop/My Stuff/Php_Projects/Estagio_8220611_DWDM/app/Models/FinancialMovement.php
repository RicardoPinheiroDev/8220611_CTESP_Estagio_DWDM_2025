<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $client_id
 * @property string|null $invoice_id
 * @property string $type
 * @property float $amount
 * @property string|null $description
 * @property string|null $payment_method
 * @property string|null $reference_number
 * @property float $balance_after
 * @property string $created_by
 * @property \Illuminate\Support\Carbon $processed_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\Invoice|null $invoice
 * @property-read \App\Models\User $createdBy
 * @property-read string $type_color
 * @property-read string $formatted_amount
 */
class FinancialMovement extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'client_id',
        'invoice_id',
        'type',
        'amount',
        'description',
        'payment_method',
        'status',
        'paid_at',
        'reference_number',
        'bank_iban',
        'account_holder',
        'mbway_phone',
        'mbway_reference',
        'paypal_email',
        'paypal_transaction_id',
        'balance_after',
        'created_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'processed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePayments($query)
    {
        return $query->where('type', 'payment');
    }

    public function scopeAdjustments($query)
    {
        return $query->where('type', 'adjustment');
    }

    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeRefunds($query)
    {
        return $query->where('type', 'refund');
    }

    public function getTypeColorAttribute()
    {
        return match ($this->type) {
            'payment' => 'success',
            'credit' => 'info',
            'adjustment' => 'warning',
            'refund' => 'danger',
            default => 'gray',
        };
    }

    public function getFormattedAmountAttribute()
    {
        $amount = (float) $this->amount;
        $prefix = $amount >= 0 ? '+' : '';
        return $prefix . '€' . number_format($amount, 2);
    }
}