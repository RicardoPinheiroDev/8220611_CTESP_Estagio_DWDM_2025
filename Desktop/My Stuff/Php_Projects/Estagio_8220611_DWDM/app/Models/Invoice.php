<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'client_id',
        'invoice_number',
        'description',
        'amount',
        'due_date',
        'status',
        'paid_at',
        'invoice_file',
        'receipt_file',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function markAsOverdue()
    {
        $this->update([
            'status' => 'overdue',
        ]);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'paid' => 'success',
            'overdue' => 'danger',
        };
    }
}