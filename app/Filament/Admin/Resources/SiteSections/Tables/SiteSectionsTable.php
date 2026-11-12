<?php

namespace App\Filament\Admin\Resources\SiteSections\Tables;

use App\Models\SiteSection;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SiteSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order')
            ->paginated(false)
            ->modifyQueryUsing(fn ($query) => $query->withCount('images'))
            ->columns([
                TextColumn::make('order')->label('Ordem')->alignCenter()->width('80px'),
                TextColumn::make('label')->label('Seção')->weight('bold')
                    ->description(fn (SiteSection $record) => $record->title),
                TextColumn::make('images_count')->label('Fotos')->alignCenter()->badge()->color('gray')
                    ->state(fn (SiteSection $record) => $record->acceptsImages() ? $record->images_count : '—'),
                ToggleColumn::make('is_active')->label('Visível')->alignCenter()
                    ->disabled(fn (SiteSection $record) => $record->key === 'hero'),
            ])
            ->recordActions([
                EditAction::make()->label('Editar')->button(),
                Action::make('fotos')->label('Fotos')->icon(Heroicon::OutlinedPhoto)->button()->color('success')
                    ->visible(fn (SiteSection $record) => $record->acceptsImages())
                    ->url(fn (SiteSection $record) => \App\Filament\Admin\Resources\SiteSections\SiteSectionResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
