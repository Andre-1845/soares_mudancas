<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Lead;
use App\Models\SiteEvent;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $start = now()->startOfMonth();
        $prevStart = now()->subMonthNoOverflow()->startOfMonth();
        $prevEnd = now()->subMonthNoOverflow()->endOfMonth();

        $novos = Lead::where('status', 'novo')->count();
        $mes = Lead::where('created_at', '>=', $start)->count();
        $mesAnt = Lead::whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $wa = SiteEvent::where('type', 'whatsapp_click')->where('created_at', '>=', $start)->count();
        $fechados = Lead::where('status', 'fechado')->where('created_at', '>=', $start)->count();
        $valor = Lead::where('status', 'fechado')->where('created_at', '>=', $start)->sum('quoted_value');

        $daily = fn ($q) => collect(range(13, 0))->map(fn ($d) => (clone $q)->whereDate('created_at', now()->subDays($d)->toDateString())->count())->all();

        return [
            Stat::make('Aguardando resposta', $novos)
                ->description($novos ? 'Responda o quanto antes' : 'Tudo em dia')
                ->color($novos ? 'warning' : 'success')
                ->url(\App\Filament\Admin\Resources\Leads\LeadResource::getUrl('index', ['filters' => ['status' => ['values' => ['novo']]]])),
            Stat::make('Orçamentos no mês', $mes)
                ->description($mesAnt ? sprintf('%+d%% vs. mês anterior', round(($mes - $mesAnt) / $mesAnt * 100)) : 'Pedidos pelo formulário')
                ->chart($daily(Lead::query()))
                ->color('primary'),
            Stat::make('Cliques no WhatsApp (mês)', $wa)
                ->description('Conversas iniciadas pelo site')
                ->chart($daily(SiteEvent::where('type', 'whatsapp_click')))
                ->color('success'),
            Stat::make('Fechados no mês', $fechados)
                ->description('R$ '.number_format((float) $valor, 2, ',', '.').' orçados')
                ->color('success'),
        ];
    }
}
