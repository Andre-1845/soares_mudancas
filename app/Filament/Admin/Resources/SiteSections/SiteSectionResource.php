<?php

namespace App\Filament\Admin\Resources\SiteSections;

use App\Filament\Admin\Resources\SiteSections\Pages\EditSiteSection;
use App\Filament\Admin\Resources\SiteSections\Pages\ListSiteSections;
use App\Filament\Admin\Resources\SiteSections\RelationManagers\ImagesRelationManager;
use App\Filament\Admin\Resources\SiteSections\Schemas\SiteSectionForm;
use App\Filament\Admin\Resources\SiteSections\Tables\SiteSectionsTable;
use App\Models\SiteSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class SiteSectionResource extends Resource
{
    protected static ?string $slug = 'secoes';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = SiteSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Conteúdo do site';

    protected static ?string $modelLabel = 'seção';

    protected static ?string $pluralModelLabel = 'Seções da página';

    protected static ?string $navigationLabel = 'Seções da página';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return SiteSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiteSectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [ImagesRelationManager::class];
    }

    // As seções são fixas (cada uma tem um layout no site): só editar.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteSections::route('/'),
            'edit' => EditSiteSection::route('/{record}/edit'),
        ];
    }
}
