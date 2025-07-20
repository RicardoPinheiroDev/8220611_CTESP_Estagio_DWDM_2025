<?php

namespace App\Observers;

use App\Models\Hosting;
use App\Models\Notification;

class HostingObserver
{
    public function created(Hosting $hosting): void
    {
        $this->createNotification(
            $hosting->client_id,
            'hosting_created',
            'Hosting Account Created',
            'Your hosting account "' . $hosting->account_name . '" has been successfully created and is now active.'
        );
    }

    public function updated(Hosting $hosting): void
    {
        if ($hosting->isDirty('status')) {
            $type = 'general';
            if ($hosting->status === 'suspended') {
                $type = 'hosting_suspended';
            } elseif ($hosting->status === 'active') {
                $type = 'hosting_created';
            }
            
            $this->createNotification(
                $hosting->client_id,
                $type,
                'Hosting Account Updated',
                'Your hosting account "' . $hosting->account_name . '" status has been changed to ' . $hosting->status . '.'
            );
        }

        if ($hosting->isDirty('payment_status')) {
            $this->createNotification(
                $hosting->client_id,
                'general',
                'Hosting Payment Status Updated',
                'Your hosting account "' . $hosting->account_name . '" payment status has been changed to ' . $hosting->payment_status . '.'
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
