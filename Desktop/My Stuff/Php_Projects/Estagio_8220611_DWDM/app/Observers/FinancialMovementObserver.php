<?php

namespace App\Observers;

use App\Models\FinancialMovement;
use App\Models\Notification;

class FinancialMovementObserver
{
    public function created(FinancialMovement $financialMovement): void
    {
        $this->createNotification(
            $financialMovement->client_id,
            'general',
            'New Financial Movement',
            "A new {$financialMovement->type} of €{$financialMovement->amount} has been recorded for your account."
        );
    }

    public function updated(FinancialMovement $financialMovement): void
    {
        if ($financialMovement->isDirty(['amount', 'type', 'description', 'payment_method'])) {
            $this->createNotification(
                $financialMovement->client_id,
                'general',
                'Financial Movement Updated',
                "Your {$financialMovement->type} of €{$financialMovement->amount} has been updated."
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