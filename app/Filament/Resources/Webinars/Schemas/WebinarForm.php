<?php

namespace App\Filament\Resources\Webinars\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class WebinarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Webinar Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                DateTimePicker::make('schedule')
                                    ->required(),
                                TextInput::make('link')
                                    ->url()
                                    ->required(),
                            ]),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        FileUpload::make('thumbnail')
                            ->image()
                            ->directory('webinars')
                            ->columnSpanFull(),
                        Toggle::make('is_premium')
                            ->default(true),
                    ]),
            ]);
    }
}
