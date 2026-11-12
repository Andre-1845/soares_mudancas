<?php

namespace App\Filament\Admin\Resources\SiteNotices\Tables;

use App\Models\SiteNotice;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SiteNoticesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                TextColumn::make('title')->label('Aviso')->weight('bold')->searchable()
                    ->description(fn (SiteNotice $record) => str($record->text)->limit(70)),
                TextColumn::make('periodo')->label('Período')
                    ->state(fn (SiteNotice $record) => ($record->starts_at?->format('d/m/Y') ?? 'Já') . ' → ' . ($record->ends_at?->format('d/m/Y') ?? 'sem fim')),
                TextColumn::make('situacao')->label('No site agora?')->badge()
                    ->state(fn (SiteNotice $record) => $record->isCurrent() ? 'No ar' : ($record->is_active && $record->starts_at?->isFuture() ? 'Agendado' : 'Fora do ar'))
                    ->color(fn (string $state) => match ($state) { 'No ar' => 'success', 'Agendado' => 'info', default => 'gray' }),
                IconColumn::make('show_in_bar')->label('Faixa do topo')->boolean()->alignCenter(),
                ToggleColumn::make('is_active')->label('Ativo'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->emptyStateHeading('Nenhum aviso')
            ->emptyStateDescription('Crie promoções e comunicados com data para entrar e sair do ar.');
    }
}
