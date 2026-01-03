<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Coupon Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                                    ->dehydrateStateUsing(fn (string $state): string => \Illuminate\Support\Str::upper($state)),
                                TextInput::make('value')
                                    ->required()
                                    ->numeric()
                                    ->label('Percentage (%)')
                                    ->suffix('%')
                                    ->maxValue(100)
                                    ->minValue(0),
                                DateTimePicker::make('expires_at'),
                                TextInput::make('usage_limit')
                                    ->numeric()
                                    ->label('Usage Limit (Optional)'),
                            ]),
                    ]),
            ]);
    }
}
