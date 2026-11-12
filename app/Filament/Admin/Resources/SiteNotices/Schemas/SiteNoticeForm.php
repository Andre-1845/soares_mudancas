<?php

namespace App\Filament\Admin\Resources\SiteNotices\Schemas;

use App\Models\SiteNotice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteNoticeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Section::make('Aviso')->columnSpan(2)->columns(2)->schema([
                TextInput::make('title')->label('Título')->required()->maxLength(120)->columnSpanFull()
                    ->placeholder('Ex.: 10% de desconto em mudanças de segunda a quinta'),
                Textarea::make('text')->label('Texto')->rows(3)->maxLength(600)->columnSpanFull(),
                TextInput::make('link_label')->label('Texto do botão (opcional)')->maxLength(40)->placeholder('Pedir orçamento'),
                TextInput::make('link_url')->label('Link do botão (opcional)')->maxLength(255)
                    ->placeholder('#orcamento ou https://...')
                    ->helperText('Use #orcamento para levar ao formulário.'),
            ]),
            Section::make('Exibição')->columnSpan(1)->schema([
                Toggle::make('is_active')->label('Ativo')->default(true),
                Toggle::make('show_in_bar')->label('Destacar na faixa do topo do site')
                    ->helperText('Aparece numa faixa acima do menu. Só o primeiro aviso destacado é exibido.'),
                Select::make('style')->label('Cor')->options(SiteNotice::STYLES)->default('promo')->required()->native(false),
                DatePicker::make('starts_at')->label('Mostrar a partir de')->native(false)->displayFormat('d/m/Y'),
                DatePicker::make('ends_at')->label('Mostrar até')->native(false)->displayFormat('d/m/Y')
                    ->afterOrEqual('starts_at')
                    ->helperText('Vazio = sem data para sair do ar.'),
                TextInput::make('order')->label('Ordem')->numeric()->default(0),
            ]),
        ]);
    }
}
