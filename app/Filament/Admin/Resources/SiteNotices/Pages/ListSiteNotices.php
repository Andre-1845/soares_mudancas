<?php

namespace App\Filament\Admin\Resources\SiteNotices\Pages;

use App\Filament\Admin\Resources\SiteNotices\SiteNoticeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSiteNotices extends ListRecords
{
    protected static string $resource = SiteNoticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
