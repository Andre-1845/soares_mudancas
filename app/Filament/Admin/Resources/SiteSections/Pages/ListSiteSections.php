<?php

namespace App\Filament\Admin\Resources\SiteSections\Pages;

use App\Filament\Admin\Resources\SiteSections\SiteSectionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListSiteSections extends ListRecords
{
    protected static string $resource = SiteSectionResource::class;

    public function getSubheading(): ?string
    {
        return 'Arraste para mudar a ordem, ligue/desligue e edite o conteúdo de cada seção da página inicial.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('site')->label('Ver o site')->color('gray')->icon('heroicon-o-globe-alt')->url(url('/'))->openUrlInNewTab(),
        ];
    }
}
