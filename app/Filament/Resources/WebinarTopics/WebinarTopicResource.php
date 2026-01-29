<?php

namespace App\Filament\Resources\WebinarTopics;

use App\Filament\Resources\WebinarTopics\Pages\CreateWebinarTopic;
use App\Filament\Resources\WebinarTopics\Pages\EditWebinarTopic;
use App\Filament\Resources\WebinarTopics\Pages\ListWebinarTopics;
use App\Filament\Resources\WebinarTopics\Schemas\WebinarTopicForm;
use App\Filament\Resources\WebinarTopics\Tables\WebinarTopicsTable;
use App\Models\WebinarTopic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WebinarTopicResource extends Resource
{
    protected static ?string $model = WebinarTopic::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Request Topik';

    protected static ?string $modelLabel = 'Request Topik';

    protected static ?string $pluralModelLabel = 'Request Topik';

    protected static ?string $recordTitleAttribute = 'yes';

    public static function form(Schema $schema): Schema
    {
        return WebinarTopicForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WebinarTopicsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWebinarTopics::route('/'),
            'create' => CreateWebinarTopic::route('/create'),
            'edit' => EditWebinarTopic::route('/{record}/edit'),
        ];
    }
}
