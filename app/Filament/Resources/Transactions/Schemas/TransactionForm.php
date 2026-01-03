<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaction Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->disabled(),
                                Select::make('package_id')
                                    ->relationship('package', 'name')
                                    ->disabled(),
                                TextInput::make('midtrans_id')
                                    ->disabled(),
                                TextInput::make('amount')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled(),
                                Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'failed' => 'Failed',
                                        'expired' => 'Expired',
                                        'challenge' => 'Challenge',
                                    ])
                                    ->required(),
                                TextInput::make('payment_type')
                                    ->disabled(),
                            ]),
                        KeyValue::make('payload')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
