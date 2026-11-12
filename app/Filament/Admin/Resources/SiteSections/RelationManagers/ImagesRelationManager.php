<?php

namespace App\Filament\Admin\Resources\SiteSections\RelationManagers;

use App\Models\SiteSection;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Fotos da seção';

    protected static ?string $modelLabel = 'foto';

    protected static ?string $pluralModelLabel = 'fotos';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof SiteSection && $ownerRecord->acceptsImages();
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order')
            ->paginated(false)
            ->contentGrid(['md' => 3, 'xl' => 4])
            ->columns([
                Stack::make([
                    ImageColumn::make('path')->disk('public')->imageHeight(150)
                        ->extraImgAttributes(['class' => 'rounded-lg object-cover w-full', 'style' => 'width:100%']),
                    TextInputColumn::make('caption')->placeholder('Legenda (opcional)'),
                ])->space(2),
            ])
            ->headerActions([
                Action::make('upload')
                    ->label('Enviar fotos')
                    ->icon(Heroicon::OutlinedArrowUpTray)
                    ->schema([
                        FileUpload::make('files')->label('Fotos')->image()->multiple()->reorderable()
                            ->disk('public')->directory('site')->maxSize(6144)->maxFiles(20)->required()
                            ->helperText('Pode selecionar várias de uma vez. JPG, PNG ou WEBP até 6 MB.'),
                    ])
                    ->action(function (array $data) {
                        $next = (int) $this->getOwnerRecord()->images()->max('order') + 1;
                        foreach ($data['files'] as $i => $path) {
                            $this->getOwnerRecord()->images()->create(['path' => $path, 'order' => $next + $i]);
                        }
                        Notification::make()->title(count($data['files']).' foto(s) adicionada(s)')->success()->send();
                    }),
            ])
            ->recordActions([
                DeleteAction::make()->label('Remover'),
            ])
            ->emptyStateHeading('Nenhuma foto nesta seção')
            ->emptyStateDescription('Clique em "Enviar fotos". Fotos reais do caminhão e da equipe passam mais confiança.');
    }
}
