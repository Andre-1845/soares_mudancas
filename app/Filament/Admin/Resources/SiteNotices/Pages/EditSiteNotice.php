<?php

namespace App\Filament\Admin\Resources\SiteNotices\Pages;

use App\Filament\Admin\Resources\SiteNotices\SiteNoticeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSiteNotice extends EditRecord
{
    protected static string $resource = SiteNoticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
