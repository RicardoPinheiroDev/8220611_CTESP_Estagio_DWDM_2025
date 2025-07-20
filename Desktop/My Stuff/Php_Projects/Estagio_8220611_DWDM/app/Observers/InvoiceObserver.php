<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\Notification;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        $this->createNotification(
            $invoice->client_id,
            'invoice_created',
            'New Invoice Created',
            'A new invoice #' . $invoice->invoice_number . ' has been created for you.'
        );
    }
    public function updated(Invoice $invoice): void
    {
        if ($invoice->isDirty('status')) {
            $type = 'invoice_' . $invoice->status;
            $this->createNotification(
                $invoice->client_id,
                $type,
                'Invoice Status Updated',
                'The status of your invoice #' . $invoice->invoice_number . ' has been updated to ' . $invoice->status . '.'
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
