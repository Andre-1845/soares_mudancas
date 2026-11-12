<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\Services\ServiceResource;
use App\Filament\Admin\Resources\SiteNotices\SiteNoticeResource;
use App\Filament\Admin\Resources\SiteSections\SiteSectionResource;
use App\Filament\Admin\Resources\Testimonials\TestimonialResource;
use App\Models\Service;
use App\Models\SiteImage;
use App\Models\SiteNotice;
use App\Models\SiteSection;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** Página central do CMS, em cards (como "Conteúdo do Site" do projeto dízimo). */
class SiteContent extends Page
{
    protected string $view = 'filament.pages.site-content';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Conteúdo do site';

    protected static ?string $navigationLabel = 'Visão geral';

    protected static ?string $title = 'Conteúdo do site';

    protected static ?string $slug = 'conteudo-do-site';

    protected static ?int $navigationSort = 1;

    public function getSubheading(): ?string
    {
        return 'Tudo o que aparece no site público. Escolha o que deseja editar.';
    }

    protected function getViewData(): array
    {
        $sections = SiteSection::query();

        return [
            'cards' => [
                [
                    'title' => 'Seções da página',
                    'text' => 'Ordem, visibilidade e textos de cada parte da página inicial.',
                    'url' => SiteSectionResource::getUrl('index'),
                    'icon' => 'heroicon-o-squares-2x2',
                    'meta' => (clone $sections)->where('is_active', true)->count().' de '.$sections->count().' visíveis',
                    'tone' => 'blue',
                ],
                [
                    'title' => 'Fotos',
                    'text' => 'Banner do topo, foto do "Sobre" e galeria de mudanças.',
                    'url' => SiteSectionResource::getUrl('edit', ['record' => SiteSection::where('key', 'gallery')->value('id') ?? 1]),
                    'icon' => 'heroicon-o-photo',
                    'meta' => SiteImage::count().' foto(s)',
                    'tone' => 'green',
                ],
                [
                    'title' => 'Serviços',
                    'text' => 'Cartões de serviços: título, descrição, ícone ou foto.',
                    'url' => ServiceResource::getUrl('index'),
                    'icon' => 'heroicon-o-truck',
                    'meta' => Service::where('active', true)->count().' visíveis',
                    'tone' => 'blue',
                ],
                [
                    'title' => 'Avisos e promoções',
                    'text' => 'Comunicados com data para entrar e sair do ar, com faixa no topo.',
                    'url' => SiteNoticeResource::getUrl('index'),
                    'icon' => 'heroicon-o-megaphone',
                    'meta' => SiteNotice::current()->count().' no ar',
                    'tone' => 'orange',
                ],
                [
                    'title' => 'Depoimentos',
                    'text' => 'Avaliações reais de clientes exibidas no site.',
                    'url' => TestimonialResource::getUrl('index'),
                    'icon' => 'heroicon-o-chat-bubble-bottom-center-text',
                    'meta' => Testimonial::where('active', true)->count().' visíveis',
                    'tone' => 'purple',
                ],
                [
                    'title' => 'Contatos, marca e SEO',
                    'text' => 'WhatsApp, e-mail, endereço e Waze, logo, cores e Google.',
                    'url' => SiteSettings::getUrl(),
                    'icon' => 'heroicon-o-cog-6-tooth',
                    'meta' => null,
                    'tone' => 'gray',
                ],
            ],
        ];
    }
}
