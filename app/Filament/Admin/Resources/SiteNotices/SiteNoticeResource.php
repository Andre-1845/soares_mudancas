<?php

namespace App\Filament\Admin\Resources\SiteNotices;

use App\Filament\Admin\Resources\SiteNotices\Pages\CreateSiteNotice;
use App\Filament\Admin\Resources\SiteNotices\Pages\EditSiteNotice;
use App\Filament\Admin\Resources\SiteNotices\Pages\ListSiteNotices;
use App\Filament\Admin\Resources\SiteNotices\Schemas\SiteNoticeForm;
use App\Filament\Admin\Resources\SiteNotices\Tables\SiteNoticesTable;
use App\Models\SiteNotice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SiteNoticeResource extends Resource
{
    protected static ?string $slug = 'avisos';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = SiteNotice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|UnitEnum|null $navigationGroup = 'Conteúdo do site';

    protected static ?string $modelLabel = 'aviso';

    protected static ?string $pluralModelLabel = 'Avisos e promoções';

    protected static ?string $navigationLabel = 'Avisos e promoções';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return SiteNoticeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiteNoticesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteNotices::route('/'),
            'create' => CreateSiteNotice::route('/create'),
            'edit' => EditSiteNotice::route('/{record}/edit'),
        ];
    }
}
