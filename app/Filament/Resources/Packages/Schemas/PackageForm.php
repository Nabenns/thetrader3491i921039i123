<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Package Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp'),
                                Toggle::make('is_lifetime')
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, bool $state) => $state ? $set('duration_in_days', null) : null),
                                TextInput::make('duration_in_days')
                                    ->numeric()
                                    ->suffix('Days')
                                    ->hidden(fn (Get $get) => $get('is_lifetime'))
                                    ->required(fn (Get $get) => ! $get('is_lifetime')),
                            ]),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->default(true),
                    ]),
                Section::make('Features')
                    ->schema([
                        Repeater::make('features')
                            ->simple(
                                TextInput::make('feature')
                                    ->required(),
                            )
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
