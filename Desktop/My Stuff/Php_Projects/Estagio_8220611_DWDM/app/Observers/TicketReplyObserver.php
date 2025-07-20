<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\TicketReply;

class TicketReplyObserver
{
    public function created(TicketReply $reply): void
    {
        if ($reply->from_client) {
            if ($reply->ticket->status === 'resolved') {
                $reply->ticket->update(['status' => 'open']);
            }
        } else {
            try {
                Notification::create([
                    'client_id' => $reply->ticket->client_id,
                    'type' => 'general',
                    'title' => 'Support Team Reply',
                    'message' => 'You got a reply from the support team for ticket #' . $reply->ticket->ticket_number . '.',
                ]);
                
                \Log::info('Notification created for client: ' . $reply->ticket->client_id . ' for ticket: ' . $reply->ticket->ticket_number);
            } catch (\Exception $e) {
                \Log::error('Failed to create notification: ' . $e->getMessage());
            }
        }
    }
}