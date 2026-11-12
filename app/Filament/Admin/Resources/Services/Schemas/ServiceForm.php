<?php

namespace App\Filament\Admin\Resources\Services\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('title')->label('Título')->required()->maxLength(80),
            TextInput::make('icon')->label('Ícone (emoji)')->maxLength(8)
                ->helperText('Cole um emoji, ex.: 🏠 📦 🚛. Usado quando não há foto.'),
            Textarea::make('description')->label('Descrição')->rows(3)->maxLength(300)->columnSpanFull(),
            FileUpload::make('image')->label('Foto (opcional)')->image()->imageEditor()
                ->disk('public')->directory('servicos')->maxSize(4096)->columnSpanFull(),
            TextInput::make('sort')->label('Ordem')->numeric()->default(0),
            Toggle::make('active')->label('Visível no site')->default(true)->inline(false),
        ]);
    }
}
