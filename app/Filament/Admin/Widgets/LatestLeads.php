<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestLeads extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Últimos pedidos de orçamento';

    public function table(Table $table): Table
    {
        return $table
            ->query(Lead::query()->latest()->limit(8))
            ->paginated(false)
            ->columns([
                TextColumn::make('created_at')->label('Recebido')->since(),
                TextColumn::make('name')->label('Nome')->weight('bold')->description(fn (Lead $r) => $r->phone),
                TextColumn::make('trajeto')->label('Trajeto')->state(fn (Lead $r) => ($r->origin ?: '?').' → '.($r->destination ?: '?')),
                TextColumn::make('status')->label('Situação')->badge()
                    ->formatStateUsing(fn ($state) => Lead::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => Lead::STATUS_COLORS[$state] ?? 'gray'),
            ])
            ->recordActions([
                Action::make('wa')->label('WhatsApp')->icon(Heroicon::OutlinedChatBubbleLeftRight)->color('success')
                    ->url(fn (Lead $r) => $r->whatsappUrl())->openUrlInNewTab(),
                Action::make('open')->label('Abrir')->url(fn (Lead $r) => LeadResource::getUrl('edit', ['record' => $r])),
            ])
            ->emptyStateHeading('Nenhum pedido ainda');
    }
}
