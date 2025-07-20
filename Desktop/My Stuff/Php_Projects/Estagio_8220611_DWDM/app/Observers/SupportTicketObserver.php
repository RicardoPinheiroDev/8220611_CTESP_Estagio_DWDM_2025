<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\SupportTicket;

class SupportTicketObserver
{
    public function created(SupportTicket $supportTicket): void
    {
        $this->createNotification(
            $supportTicket->client_id,
            'support_ticket_created',
            'Support Ticket Created',
            'Your support ticket #' . $supportTicket->ticket_number . ' has been created. You can now add replies through your panel.'
        );
    }

    public function updated(SupportTicket $supportTicket): void
    {
        
        if ($supportTicket->isDirty('status')) {
            $type = 'general';
            if ($supportTicket->status === 'resolved') {
                $type = 'support_ticket_resolved';
            }
            
            $message = $supportTicket->status === 'closed' 
                ? 'Your support ticket #' . $supportTicket->ticket_number . ' status has been closed.in '
                : 'Your support ticket #' . $supportTicket->ticket_number . ' status has been' . $supportTicket->status;
                
            $this->createNotification(
                $supportTicket->client_id,
                $type,
                'Support Ticket Status Updated',
                $message
            );
        }

        if ($supportTicket->isDirty('priority')) {
            $this->createNotification(
                $supportTicket->client_id,
                'general',
                'Support Ticket Priority Updated',
                'Your support ticket #' . $supportTicket->ticket_number . ' priority has been changed to ' . $supportTicket->priority . '.'
            );
        }

        if ($supportTicket->isDirty('admin_notes') && !empty($supportTicket->admin_notes)) {
            $this->createNotification(
                $supportTicket->client_id,
                'general',
                'Support Ticket Updated',
                'Your support ticket #' . $supportTicket->ticket_number . ' has been updated. Please check for new information.'
            );
        }
    }

    protected function createNotification(string $clientId, string $type, string $title, string $message): void
    {
        Notification::create([
            'client_id' => $clientId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ]);
    }
}
