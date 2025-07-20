<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $ticket_number
 * @property string $client_id
 * @property string $subject
 * @property string $description
 * @property string $status
 * @property string $priority
 * @property string $service_type
 * @property string $service_id
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Client $client
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\TicketReply> $replies
 * @property-read Model|\Eloquent $service
 * @property-read string $service_name
 */
class SupportTicket extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'ticket_number',
        'client_id',
        'subject',
        'description',
        'status',
        'priority',
        'service_type',
        'service_id',
        'admin_notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id')->orderBy('created_at');
    }

    public function service()
    {
        return match ($this->service_type) {
            'domain' => $this->belongsTo(Domain::class, 'service_id'),
            'hosting' => $this->belongsTo(Hosting::class, 'service_id'),
            default => null,
        };
    }

    public function getServiceNameAttribute()
    {
        return match ($this->service_type) {
            'domain' => $this->service?->name ?? 'Unknown Domain',
            'hosting' => $this->service?->account_name ?? 'Unknown Hosting',
            default => 'General Support',
        };
    }

    public function getTotalTimeSpentAttribute()
    {
        return $this->timeEntries()->sum('hours_spent');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'open' => 'danger',
            'in_progress' => 'warning',
            'resolved' => 'success',
            'closed' => 'gray',
        };
    }

    public function getPriorityColorAttribute()
    {
        return match ($this->priority) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'urgent' => 'danger',
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = static::generateTicketNumber();
            }
        });
    }

    public static function generateTicketNumber(): string
    {
        $lastTicket = static::latest()->first();
        $nextNumber = $lastTicket ? (int)str_replace('TICKET-', '', $lastTicket->ticket_number) + 1 : 1;
        return 'TICKET-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function getLastReplyAttribute()
    {
        return $this->replies()->latest()->first();
    }

    public function getConversationAttribute()
    {
        return $this->replies()->with('ticket')->get();
    }
}