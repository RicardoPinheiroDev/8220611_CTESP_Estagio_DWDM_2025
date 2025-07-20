<?php

namespace App\Observers;

use App\Models\Domain;
use App\Models\Notification;

class DomainObserver
{
    public function created(Domain $domain): void
    {
        $this->createNotification(
            $domain->client_id,
            'domain_created',
            'Domain Created',
            'Your domain "' . $domain->name . '" has been successfully created and is now active.'
        );
    }

    public function updated(Domain $domain): void
    {
        if ($domain->isDirty('status')) {
            $type = match ($domain->status) {
                'expired' => 'domain_expired',
                'expiring' => 'domain_expiring',
                default => 'general'
            };
            
            $this->createNotification(
                $domain->client_id,
                $type,
                'Domain Status Updated',
                'Your domain "' . $domain->name . '" status has been changed to ' . $domain->status . '.'
            );
        }

        if ($domain->isDirty('expires_at')) {
            $this->createNotification(
                $domain->client_id,
                'domain_renewed',
                'Domain Renewed',
                'Your domain "' . $domain->name . '" has been renewed until ' . $domain->expires_at->format('Y-m-d') . '.'
            );
        }

        if ($domain->isDirty('payment_status')) {
            $this->createNotification(
                $domain->client_id,
                'general',
                'Domain Payment Status Updated',
                'Your domain "' . $domain->name . '" payment status has been changed to ' . $domain->payment_status . '.'
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
