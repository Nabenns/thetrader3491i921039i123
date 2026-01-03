<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subscription Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('package_id')
                                    ->relationship('package', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                DateTimePicker::make('starts_at')
                                    ->required(),
                                DateTimePicker::make('ends_at'),
                                Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'expired' => 'Expired',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->required()
                                    ->default('active'),
                            ]),
                    ]),
            ]);
    }
}
