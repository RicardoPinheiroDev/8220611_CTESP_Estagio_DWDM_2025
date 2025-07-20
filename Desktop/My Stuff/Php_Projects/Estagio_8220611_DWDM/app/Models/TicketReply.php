<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'ticket_id',
        'message',
        'from_client',
        'sender_email',
    ];

    protected $casts = [
        'from_client' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function scopeFromClient($query)
    {
        return $query->where('from_client', true);
    }

    public function scopeFromAdmin($query)
    {
        return $query->where('from_client', false);
    }
}