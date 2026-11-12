<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Lead;
use App\Models\SiteEvent;
use Filament\Widgets\ChartWidget;

class LeadsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Contatos pelo site — últimos 30 dias';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn ($d) => now()->subDays($d)->startOfDay());
        $from = $days->first();

        $leads = Lead::where('created_at', '>=', $from)->get(['created_at'])
            ->countBy(fn ($l) => $l->created_at->toDateString());
        $wa = SiteEvent::where('type', 'whatsapp_click')->where('created_at', '>=', $from)->get(['created_at'])
            ->countBy(fn ($e) => $e->created_at->toDateString());

        return [
            'datasets' => [
                [
                    'label' => 'Formulários',
                    'data' => $days->map(fn ($d) => $leads[$d->toDateString()] ?? 0)->all(),
                    'borderColor' => '#0b2f5b',
                    'backgroundColor' => 'rgba(11,47,91,.12)',
                    'fill' => true,
                    'tension' => .3,
                ],
                [
                    'label' => 'Cliques no WhatsApp',
                    'data' => $days->map(fn ($d) => $wa[$d->toDateString()] ?? 0)->all(),
                    'borderColor' => '#16a34a',
                    'backgroundColor' => 'transparent',
                    'tension' => .3,
                ],
            ],
            'labels' => $days->map(fn ($d) => $d->format('d/m'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return ['scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]]];
    }
}
