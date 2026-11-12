<?php

namespace App\Filament\Admin\Resources\Leads\Tables;

use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Recebido')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('name')->label('Nome')->searchable()->weight('bold')
                    ->description(fn (Lead $r) => $r->phone),
                TextColumn::make('service_type')->label('Serviço')
                    ->formatStateUsing(fn ($state) => Lead::SERVICE_TYPES[$state] ?? $state)->toggleable(),
                TextColumn::make('route')->label('Trajeto')
                    ->state(fn (Lead $r) => trim(($r->origin ?: '?').' → '.($r->destination ?: '?')))
                    ->wrap()->toggleable(),
                TextColumn::make('move_date')->label('Data mudança')->date('d/m/Y')->sortable()->toggleable(),
                TextColumn::make('status')->label('Situação')->badge()
                    ->formatStateUsing(fn ($state) => Lead::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => Lead::STATUS_COLORS[$state] ?? 'gray'),
                TextColumn::make('quoted_value')->label('Valor')->money('BRL')->sortable()->toggleable(),
                TextColumn::make('email')->label('E-mail')->searchable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->label('Situação')->options(Lead::STATUSES)->multiple(),
                SelectFilter::make('service_type')->label('Serviço')->options(Lead::SERVICE_TYPES),
                Filter::make('periodo')
                    ->schema([
                        DatePicker::make('de')->label('Recebido de'),
                        DatePicker::make('ate')->label('até'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['de'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
                        ->when($data['ate'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color('success')
                    ->url(fn (Lead $r) => $r->whatsappUrl())
                    ->openUrlInNewTab(),
                EditAction::make()->label('Abrir'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('status')
                        ->label('Mudar situação')
                        ->icon(Heroicon::OutlinedArrowPath)
                        ->schema([
                            \Filament\Forms\Components\Select::make('status')->label('Nova situação')->options(Lead::STATUSES)->required(),
                        ])
                        ->action(fn (Collection $records, array $data) => $records->each->update(['status' => $data['status']]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Nenhum orçamento ainda')
            ->emptyStateDescription('Os pedidos enviados pelo formulário do site aparecem aqui.');
    }
}
