<?php

namespace App\Filament\Admin\Resources\Testimonials\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('name')->label('Cliente')->weight('bold')->searchable()
                    ->description(fn ($record) => $record->city),
                TextColumn::make('text')->label('Depoimento')->limit(80)->wrap(),
                TextColumn::make('rating')->label('Nota')->formatStateUsing(fn ($state) => str_repeat("★", (int) $state)),
                ToggleColumn::make('active')->label('Visível'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->paginated(false);
    }
}
