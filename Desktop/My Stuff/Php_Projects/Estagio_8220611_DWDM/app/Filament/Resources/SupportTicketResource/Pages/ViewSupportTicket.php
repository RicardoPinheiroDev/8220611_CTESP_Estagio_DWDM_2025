<?php

namespace App\Filament\Resources\SupportTicketResource\Pages;

use App\Filament\Resources\SupportTicketResource;
use App\Models\TicketReply;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSupportTicket extends ViewRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Ticket Information')
                    ->schema([
                        TextEntry::make('ticket_number')
                            ->label('Ticket ID'),
                        TextEntry::make('client.name')
                            ->label('Client'),
                        TextEntry::make('subject')
                            ->label('Subject'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'open' => 'danger',
                                'in_progress' => 'warning',
                                'resolved' => 'success',
                                'closed' => 'gray',
                            }),
                        TextEntry::make('priority')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'low' => 'success',
                                'medium' => 'warning',
                                'high' => 'danger',
                                'urgent' => 'danger',
                            }),
                        TextEntry::make('service_type')
                            ->label('Service Type')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'general' => 'General Support',
                                'domain' => 'Domain',
                                'hosting' => 'Hosting',
                                default => $state,
                            }),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                    ])
                    ->columns(2),
                
                Section::make('Original Message')
                    ->schema([
                        TextEntry::make('description')
                            ->label('')
                            ->formatStateUsing(fn (string $state): string => nl2br(e($state)))
                            ->html(),
                    ]),

                Section::make('Conversation')
                    ->headerActions([
                        Action::make('reply')
                            ->label('Send Reply')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->color('primary')
                            ->form([
                                Textarea::make('message')
                                    ->label('Your Reply')
                                    ->required()
                                    ->rows(4)
                                    ->placeholder('Type your response to the client...')
                            ])
                            ->action(function (array $data) {
                                
                                TicketReply::create([
                                    'ticket_id' => $this->record->id,
                                    'message' => $data['message'],
                                    'from_client' => false,
                                    'sender_email' => auth()->user()->email,
                                ]);

                                
                                if ($this->record->status === 'resolved') {
                                    $this->record->update(['status' => 'in_progress']);
                                }

                                Notification::make()
                                    ->title('Reply added successfully')
                                    ->body('Client will be notified through their panel')
                                    ->success()
                                    ->send();
                                    
                                $this->redirect(request()->header('Referer'));
                            }),
                    ])
                    ->schema([
                        TextEntry::make('replies')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                $replies = $record->replies()->orderBy('created_at')->get();
                                
                                if ($replies->isEmpty()) {
                                    return '<div class="h-96 overflow-y-auto p-4">
                                        <p class="text-gray-500 italic text-center">No replies yet.</p>
                                    </div>';
                                }

                                $html = '<div class="h-96 overflow-y-auto p-4 space-y-4" id="conversation-container">';
                                foreach ($replies as $reply) {
                                    $fromLabel = $reply->from_client ? 'Client' : 'Support Team';
                                    $bgColor = $reply->from_client ? 'bg-blue-50' : 'bg-green-50';
                                    $textColor = $reply->from_client ? 'text-blue-900' : 'text-green-900';
                                    
                                    $html .= '<div class="p-4 rounded-lg ' . $bgColor . '">';
                                    $html .= '<div class="flex justify-between items-center mb-2">';
                                    $html .= '<span class="font-semibold ' . $textColor . '">' . $fromLabel . '</span>';
                                    $html .= '<span class="text-sm text-gray-500">' . $reply->created_at->format('M j, Y g:i A') . '</span>';
                                    $html .= '</div>';
                                    $html .= '<div class="text-gray-800">' . nl2br(e($reply->message)) . '</div>';
                                    $html .= '</div>';
                                }
                                $html .= '</div>';
                                
                               
                                $html .= '<script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const container = document.getElementById("conversation-container");
                                        if (container) {
                                            container.scrollTop = container.scrollHeight;
                                        }
                                    });
                                </script>';
                                
                                return $html;
                            })
                            ->html(),
                    ])
                    ->visible(fn ($record) => $record->replies()->exists()),
            ]);
    }
}