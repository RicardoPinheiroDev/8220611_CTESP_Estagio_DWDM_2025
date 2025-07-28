<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\FAQResource\Pages;
use App\Models\Client;
use Filament\Resources\Resource;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FAQResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationLabel = 'FAQ';

    protected static ?string $pluralLabel = 'Frequently Asked Questions';
    
    protected static ?int $navigationSort = -5;

    public static function infolist(Infolist $infolist): Infolist
    {
        // Define FAQ sections and entries in a single array
        $faqs = [
            '🌐 Getting Started with DigiUp' => [
                ['platform_q1', 'What is DigiUp?', 'DigiUp is a web development company that creates professional websites for businesses looking to establish their online presence. As part of our comprehensive service, we also manage the hosting and domain services for the websites we build, ensuring your online presence stays active and secure.'],
                ['platform_q2', 'What is this client management platform?', 'This platform allows you to manage the hosting and domain services for the website DigiUp created for your company. Through this dashboard, you can monitor your services, track renewal dates, manage payments, and get technical support for your website\'s infrastructure.'],
                ['platform_q3', 'Why do I need this platform if DigiUp built my website?', 'While DigiUp handles the technical aspects of your website, this platform gives you visibility and control over your hosting and domain services. You can track costs, renewal dates, service usage, and communicate with our team about any technical needs or concerns regarding your website\'s infrastructure.'],
            ],
            '🏢 Domain Management' => [
                ['domain_q1', 'How do I manage my domain renewals through DigiUp?', 'DigiUp automatically tracks your domain expiration dates and sends you renewal notifications well in advance. You can view all your domains, their expiration dates, and renew them directly through your dashboard. We also offer auto-renewal options to ensure your domains never expire unexpectedly.'],
                ['domain_q2', 'What happens if my domain expires?', 'If your domain expires, your website and email services will stop working. DigiUp sends multiple reminder notifications before expiration. If a domain does expire, we can help you recover it during the grace period, though additional fees may apply.'],
                ['domain_q3', 'Can I transfer my existing domains to DigiUp management?', 'Yes! DigiUp can take over management of your existing domains. We\'ll handle the transfer process and ensure there\'s no downtime. Contact our support team to initiate a domain transfer to our management system.'],
            ],
            '💼 Hosting Services' => [
                ['hosting_q1', 'What hosting services does DigiUp provide?', 'DigiUp offers comprehensive hosting management including shared hosting, VPS, dedicated servers, and cloud hosting solutions. We handle server maintenance, security updates, backups, and performance optimization so you can focus on your business.'],
                ['hosting_q2', 'How can I monitor my hosting usage and performance?', 'Your DigiUp dashboard provides real-time insights into your hosting usage, including bandwidth consumption, storage space, and performance metrics. You can also set up alerts for when usage approaches your plan limits.'],
                ['hosting_q3', 'What if I need to upgrade my hosting plan?', 'Upgrading your hosting plan is easy through the DigiUp platform. Simply navigate to your hosting services, select the upgrade option, and choose your new plan. We\'ll handle the migration process with minimal downtime.'],
            ],
            '💳 Billing and Payments' => [
                ['billing_q1', 'What payment methods does DigiUp accept?', 'DigiUp accepts multiple payment methods including bank transfers, MbWay, and PayPal. You can set up automatic payments to ensure your services are never interrupted due to late payments.'],
                ['billing_q2', 'How do I view my invoices and payment history?', 'All your invoices and payment history are available in the Financial Movements section of your dashboard. You can download invoices, track payments, and view your account balance at any time.'],
                ['billing_q3', 'Can I get consolidated billing for multiple services?', 'Yes! DigiUp provides consolidated billing for all your services. You\'ll receive a single invoice that includes all your hosting and domain services, making it easier to manage your expenses and accounting.'],
            ],
            '🎯 Support & Assistance' => [
                ['support_q1', 'How do I get technical support from DigiUp?', 'You can create support tickets directly through your dashboard. Our technical team will respond promptly to help resolve any issues with your hosting or domain services. We also provide phone and email support for urgent matters.'],
                ['support_q2', 'What are DigiUp\'s support hours?', 'DigiUp provides business hours support Monday through Friday, 9 AM to 6 PM. For critical issues affecting your website availability, we offer emergency support contact options available 24/7.'],
                ['support_q3', 'Do you provide migration services?', 'Yes! DigiUp offers free migration services when you sign up for our hosting management. Our technical team will handle moving your website, databases, and email accounts from your current provider to ensure a smooth transition.'],
            ],
        ];

        $sections = [];

        foreach ($faqs as $title => $questions) {
            $entries = [];

            foreach ($questions as [$key, $label, $state]) {
                $entries[] = TextEntry::make($key)
                    ->label($label)
                    ->state($state);
            }

            $sections[] = Section::make($title)
                ->schema($entries)
                ->collapsible()
                ->collapsed();
        }

        return $infolist->schema($sections);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ViewFAQ::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Since this is an FAQ resource, we don't need to filter by client
        // as FAQs are typically global/shared content
        return parent::getEloquentQuery();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
