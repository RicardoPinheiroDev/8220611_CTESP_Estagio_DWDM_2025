<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RenewalsResource\Pages;
use App\Models\Renewal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RenewalsResource extends Resource
{
    protected static ?string $model = Renewal::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Services';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('renewable_type')
                    ->label('Resource Type')
                    ->options([
                        'App\\Models\\Domain' => 'Domain',
                        'App\\Models\\Hosting' => 'Hosting',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('renewable_id', null)),
                
                Forms\Components\Select::make('renewable_id')
                    ->label('Select Resource')
                    ->options(function (callable $get) {
                        $type = $get('renewable_type');
                        if (!$type) {
                            return [];
                        }
                        return $type::query()
                            ->select('id', 'name', 'domain')
                            ->get()
                            ->mapWithKeys(fn ($item) => [
                                $item->id => $item->name . ' - ' . $item->domain
                            ]);
                    })
                    ->getSearchResultsUsing(function (string $search, callable $get) {
                        $type = $get('renewable_type');
                        if (!$type) {
                            return [];
                        }
                        return $type::query()
                            ->where(function($query) use ($search) {
                                $query->where('name', 'like', "%{$search}%")
                                      ->orWhere('domain', 'like', "%{$search}%");
                            })
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn ($item) => [
                                $item->id => $item->name . ' - ' . $item->domain
                            ])
                            ->toArray();
                    })
                    ->getOptionLabelUsing(fn ($value, callable $get): ?string => 
                        ($model = $get('renewable_type')::find($value)) 
                            ? $model->name . ' - ' . $model->domain 
                            : $value
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('domain')
                            ->required()
                            ->maxLength(255),
                    ]),
                
                Forms\Components\DateTimePicker::make('renewed_at')
                    ->label('Renewal Date')
                    ->required()
                    ->default(now()),
                    
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->prefix('€')
                    ->step(0.01),
                    
                Forms\Components\Select::make('payment_method')
                    ->options([
                        'Transferência' => 'Transferência',
                        'MBWay' => 'MBWay',
                        'Cartão' => 'Cartão',
                        'PayPal' => 'PayPal',
                        'Crypto' => 'Cryptocurrency',
                    ])
                    ->required(),
                    
                Forms\Components\FileUpload::make('receipt_file')
                    ->label('Customer Receipt (optional)')
                    ->directory('renewals/receipts')
                    ->downloadable()
                    ->openable()
                    ->previewable()
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(2048)
                    ->columnSpanFull(),
                    
                Forms\Components\FileUpload::make('internal_file')
                    ->label('Internal Document (optional)')
                    ->directory('renewals/internal')
                    ->downloadable()
                    ->openable()
                    ->previewable()
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(2048)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('renewable.name')
                    ->label('Resource')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('renewable_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('renewed_at')
                    ->dateTime()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->money('EUR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('renewable_type')
                    ->options([
                        'App\\Models\\Domain' => 'Domain',
                        'App\\Models\\Hosting' => 'Hosting',
                    ])
                    ->label('Type'),
                    
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'Transferência' => 'Transferência',
                        'MBWay' => 'MBWay',
                        'Cartão' => 'Cartão',
                        'PayPal' => 'PayPal',
                        'Crypto' => 'Cryptocurrency',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRenewals::route('/'),
            'create' => Pages\CreateRenewals::route('/create'),
            'view' => Pages\ViewRenewals::route('/{record}'),
            'edit' => Pages\EditRenewals::route('/{record}/edit'),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['renewable.name', 'payment_method', 'amount'];
    }
    
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (!($record instanceof Renewal)) {
            return [];
        }
        
        return [
            'Resource' => $record->renewable?->name ?? 'N/A',
            'Type' => $record->renewable_type ? class_basename($record->renewable_type) : 'N/A',
            'Amount' => $record->amount ? '€' . number_format($record->amount, 2) : 'N/A',
        ];
    }
}