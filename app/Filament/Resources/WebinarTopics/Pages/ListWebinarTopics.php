<?php

namespace App\Filament\Resources\WebinarTopics\Pages;

use App\Filament\Resources\WebinarTopics\WebinarTopicResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWebinarTopics extends ListRecords
{
    protected static string $resource = WebinarTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
