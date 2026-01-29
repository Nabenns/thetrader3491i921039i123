<?php

namespace App\Filament\Resources\WebinarTopics\Schemas;

use Filament\Schemas\Schema;

class WebinarTopicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->disabled()
                    ->required(),
                \Filament\Forms\Components\Textarea::make('topic')
                    ->rows(5)
                    ->disabled()
                    ->required()
                    ->columnSpanFull(),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->default('pending'),
            ]);
    }
}
