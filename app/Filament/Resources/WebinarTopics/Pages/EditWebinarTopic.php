<?php

namespace App\Filament\Resources\WebinarTopics\Pages;

use App\Filament\Resources\WebinarTopics\WebinarTopicResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWebinarTopic extends EditRecord
{
    protected static string $resource = WebinarTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
