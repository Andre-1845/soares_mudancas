<?php

namespace App\Filament\Admin\Resources\Testimonials\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label('Nome do cliente')->required()->maxLength(80),
            TextInput::make('city')->label('Cidade / bairro')->maxLength(80),
            Textarea::make('text')->label('Depoimento')->required()->rows(4)->maxLength(600)->columnSpanFull(),
            Select::make('rating')->label('Nota')->options([5 => '★★★★★', 4 => '★★★★', 3 => '★★★'])->default(5)->required(),
            TextInput::make('source')->label('Origem')->placeholder('Google, WhatsApp, Instagram...')->maxLength(40),
            TextInput::make('sort')->label('Ordem')->numeric()->default(0),
            Toggle::make('active')->label('Visível no site')->inline(false)
                ->helperText('Publique apenas depoimentos reais, com autorização do cliente.'),
        ]);
    }
}
