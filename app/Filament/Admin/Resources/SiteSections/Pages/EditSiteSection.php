<?php

namespace App\Filament\Admin\Resources\SiteSections\Pages;

use App\Filament\Admin\Resources\SiteSections\SiteSectionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditSiteSection extends EditRecord
{
    protected static string $resource = SiteSectionResource::class;

    public function getTitle(): string
    {
        return 'Seção: '.$this->record->label;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ver')->label('Ver no site')->color('gray')->icon('heroicon-o-eye')
                ->url(url('/').'#'.$this->record->anchor())->openUrlInNewTab(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Seção atualizada! O site já mostra as mudanças.';
    }
}
