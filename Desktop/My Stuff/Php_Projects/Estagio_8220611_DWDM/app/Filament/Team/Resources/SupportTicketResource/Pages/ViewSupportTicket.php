<?php

namespace App\Filament\Team\Resources\SupportTicketResource\Pages;

use App\Filament\Team\Resources\SupportTicketResource;
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
                Section::make('Conversation')
                    ->headerActions([
                        Action::make('reply')
                            ->label('Send Reply')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->color('primary')
                            ->visible(fn ($record) => !in_array($record->status, ['resolved', 'closed']))
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

                                if ($this->record->status === 'open') {
                                    $this->record->update(['status' => 'in_progress']);
                                }

                                Notification::make()
                                    ->title('Reply sent successfully')
                                    ->body('Client will see your response in their panel')
                                    ->success()
                                    ->send();
                                    
                                $this->redirect(request()->header('Referer'));
                            }),
                        Action::make('lock_conversation')
                            ->label('Lock Conversation')
                            ->icon('heroicon-o-lock-closed')
                            ->color('danger')
                            ->visible(fn ($record) => $record->status !== 'closed')
                            ->requiresConfirmation()
                            ->modalHeading('Lock Conversation')
                            ->modalDescription('Are you sure you want to lock this conversation? This will close the ticket and prevent further replies.')
                            ->action(function () {
                                $this->record->update(['status' => 'closed']);
                                
                                Notification::make()
                                    ->title('Conversation locked')
                                    ->body('This ticket has been closed and locked')
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
                                    return '<div style="height: 400px; overflow-y: auto; padding: 16px; scrollbar-width: none; -ms-overflow-style: none;">
                                        <div class="text-center p-8 text-gray-500">
                                            <p class="mb-2">💬 <strong>No replies yet</strong></p>
                                            <p class="text-sm">This ticket has no conversation yet.</p>
                                            <p class="text-sm mt-2">Use the "Send Reply" button above to respond to the client.</p>
                                        </div>
                                    </div>';
                                }

                                $html = '<div style="height: 400px; overflow-y: auto; padding: 16px; scrollbar-width: none; -ms-overflow-style: none;" id="team-conversation-container">';
                                foreach ($replies as $reply) {
                                    $fromLabel = $reply->from_client ? $record->client->name : 'Support Team';
                                    $bgColor = $reply->from_client ? 'bg-blue-50' : 'bg-green-50';
                                    $textColor = $reply->from_client ? 'text-blue-900' : 'text-green-900';
                                    
                                    $html .= '<div style="padding: 16px; border-radius: 8px; margin-bottom: 16px;" class="' . $bgColor . '">';
                                    $html .= '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">';
                                    $html .= '<span style="font-weight: 600;" class="' . $textColor . '">' . $fromLabel . '</span>';
                                    $html .= '<span style="font-size: 14px; color: #6b7280;">' . $reply->created_at->format('M j, Y g:i A') . '</span>';
                                    $html .= '</div>';
                                    $html .= '<div style="color: #ffffff;">' . nl2br(e($reply->message)) . '</div>';
                                    $html .= '</div>';
                                }
                                $html .= '</div>';
                                
                                $html .= '<style>
                                    #team-conversation-container::-webkit-scrollbar {
                                        display: none;
                                    }
                                </style>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const container = document.getElementById("team-conversation-container");
                                        if (container) {
                                            container.scrollTop = container.scrollHeight;
                                        }
                                    });
                                </script>';
                                
                                return $html;
                            })
                            ->html(),
                    ]),

                Section::make('Ticket Information')
                    ->schema([
                        TextEntry::make('ticket_number')
                            ->label('Ticket ID'),
                        TextEntry::make('client.name')
                            ->label('Client'),
                        TextEntry::make('subject')
                            ->label(''),
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
                        TextEntry::make('description')
                            ->label('Original Message')
                            ->formatStateUsing(fn (string $state): string => nl2br(e($state)))
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}