<?php

namespace App\Filament\Admin\Resources\Services\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('icon')->label(''),
                ImageColumn::make('image')->label('Foto')->disk('public'),
                TextColumn::make('title')->label('Título')->searchable()->weight('bold')
                    ->description(fn ($record) => str($record->description)->limit(70)),
                ToggleColumn::make('active')->label('Visível'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->paginated(false);
    }
}
