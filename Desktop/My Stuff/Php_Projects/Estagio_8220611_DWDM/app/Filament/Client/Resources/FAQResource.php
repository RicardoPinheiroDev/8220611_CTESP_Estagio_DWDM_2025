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
    
    protected static ?int $navigationSort = 5;

    public static function infolist(Infolist $infolist): Infolist
    {
        // Define FAQ sections and entries in a single array
        $faqs = [
            '🏢 Domain Management' => [
                ['domain_q1', 'How to manage enterprise domain renewals?', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'],
                ['domain_q2', 'What happens if my domain expires?', 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'],
                ['domain_q3', 'How to change my domain DNS settings?', 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.'],
            ],
            '💼 Hosting Services' => [
                ['hosting_q1', 'How to access enterprise hosting services?', 'Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum.'],
                ['hosting_q3', 'What is the available space on my hosting?', 'Ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat.'],
            ],
            '📊 Billing and Payments' => [
                ['billing_q1', 'What enterprise payment methods are available?', 'Quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident.'],
                ['billing_q2', 'Where can I view my invoices?', 'Similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit.'],
                ['billing_q3', 'How to request a company invoice?', 'Quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint.'],
            ],
            '🎯 Support' => [
                ['support_q1', 'How does enterprise technical support work?', 'Molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias.'],
                ['support_q2', 'What are the support operating hours?', 'Excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis.'],
                ['support_q3', 'How to check support time spent?', 'Est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet.'],
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
                ->collapsible(false);
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
