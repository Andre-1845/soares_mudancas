<?php

namespace App\Filament\Admin\Resources\SiteSections\Schemas;

use App\Models\SiteSection;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;

/**
 * Formulário único para todas as seções; os campos aparecem conforme a seção (key).
 */
class SiteSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        $is = fn (string ...$keys) => fn (?SiteSection $record) => in_array($record?->key, $keys, true);

        $subtitleLabel = fn (?SiteSection $record) => match ($record?->key) {
            'about' => 'Título da lista de diferenciais',
            'quote' => 'Texto de apresentação',
            default => 'Subtítulo',
        };

        return $schema->columns(3)->components([
            Section::make('Textos')
                ->columnSpan(2)
                ->schema([
                    TextInput::make('eyebrow')->label('Texto pequeno acima do título')->maxLength(120)
                        ->helperText('Ex.: "Quem somos", "Orçamento grátis". Deixe vazio para não mostrar.'),
                    TextInput::make('title')->label('Título')->required()->maxLength(160),
                    Textarea::make('subtitle')->label($subtitleLabel)->rows(2)->maxLength(300)
                        ->visible($is('hero', 'services', 'about', 'steps', 'gallery', 'testimonials', 'notices', 'quote', 'location')),
                    Textarea::make('content')->label('Texto principal')->rows(7)->maxLength(4000)
                        ->helperText('Pule uma linha para criar parágrafos.')
                        ->visible($is('about')),
                    TextInput::make('button_text')->label('Texto do botão')->maxLength(60)
                        ->visible($is('hero', 'quote')),

                    Repeater::make('items')
                        ->label(fn (?SiteSection $record) => match ($record?->key) {
                            'hero' => 'Destaques (com ✓)',
                            'steps' => 'Etapas',
                            default => 'Diferenciais (com ✓)',
                        })
                        ->schema(fn (?SiteSection $record) => $record?->key === 'steps'
                            ? [
                                TextInput::make('title')->label('Etapa')->required()->maxLength(50),
                                TextInput::make('text')->label('Descrição')->maxLength(140),
                            ]
                            : [TextInput::make('text')->hiddenLabel()->required()->maxLength(80)])
                        ->columns(fn (?SiteSection $record) => $record?->key === 'steps' ? 2 : 1)
                        ->grid(fn (?SiteSection $record) => $record?->key === 'steps' ? 1 : 2)
                        ->addActionLabel('Adicionar item')->reorderable()->defaultItems(0)
                        ->visible($is('hero', 'about', 'steps')),
                ]),

            Section::make('Exibição')
                ->columnSpan(1)
                ->schema([
                    Toggle::make('is_active')->label('Mostrar esta seção no site')
                        ->disabled($is('hero')),
                    TextInput::make('order')->label('Posição na página')->numeric()->minValue(1)
                        ->helperText('Ou arraste na lista de seções.'),
                    Text::make(fn (?SiteSection $record) => match ($record?->key) {
                        'services' => 'Os cartões de serviço são editados em Conteúdo do site → Serviços.',
                        'testimonials' => 'Os depoimentos são editados em Conteúdo do site → Depoimentos. A seção só aparece se houver depoimento visível.',
                        'notices' => 'Os avisos são editados em Conteúdo do site → Avisos e promoções. A seção só aparece se houver aviso dentro do período.',
                        'gallery' => 'Envie as fotos logo abaixo, em "Fotos da seção". A seção só aparece se houver fotos.',
                        'hero' => 'A primeira foto vira o fundo do topo. Com várias fotos, elas se alternam automaticamente.',
                        'about' => 'A primeira foto aparece ao lado do texto.',
                        'location' => 'Endereço, Waze e mapa são editados em Contatos, marca e SEO.',
                        'quote' => 'Os pedidos enviados chegam em Comercial → Orçamentos.',
                        default => '',
                    })->color('gray'),
                ]),
        ]);
    }
}
