<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Seções fixas da página inicial. O gestor edita textos, ordem e visibilidade,
 * mas não cria nem apaga seções (cada uma tem um layout próprio no site).
 */
class DefaultSections
{
    /** Nome curto usado no menu do site (âncoras). */
    public const MENU = [
        'services' => 'Serviços',
        'about' => 'Sobre',
        'steps' => 'Como funciona',
        'notices' => 'Avisos',
        'gallery' => 'Galeria',
        'testimonials' => 'Depoimentos',
        'location' => 'Localização',
    ];

    /** Âncora (#id) de cada seção no site. */
    public const ANCHORS = [
        'hero' => 'topo',
        'notices' => 'avisos',
        'services' => 'servicos',
        'about' => 'sobre',
        'steps' => 'como-funciona',
        'gallery' => 'galeria',
        'testimonials' => 'depoimentos',
        'quote' => 'orcamento',
        'location' => 'localizacao',
    ];

    /** Seções que aceitam fotos. */
    public const WITH_IMAGES = ['hero', 'about', 'gallery'];

    public static function definitions(array $old = []): array
    {
        $json = fn ($v) => is_string($v) ? json_decode($v, true) : $v;
        $list = fn ($v, $fallback) => array_values($json($v) ?: $fallback);

        return [
            ['hero', 'Topo da página (banner)', [
                'eyebrow' => $old['service_area'] ?? 'Resende e região Sul Fluminense',
                'title' => $old['hero_title'] ?? 'Sua mudança com segurança, rapidez e confiança',
                'subtitle' => $old['hero_subtitle'] ?? 'Mudanças residenciais, comerciais e interestaduais com equipe especializada, em Resende e região.',
                'button_text' => $old['hero_button_text'] ?? 'Pedir orçamento grátis',
                'items' => [['text' => 'Orçamento sem compromisso'], ['text' => 'Veículos próprios'], ['text' => 'Equipe treinada']],
            ]],
            ['notices', 'Avisos e promoções', [
                'eyebrow' => 'Fique por dentro', 'title' => 'Avisos e promoções',
            ]],
            ['services', 'Serviços', [
                'eyebrow' => 'O que fazemos', 'title' => 'Nossos serviços',
            ]],
            ['about', 'Sobre a empresa', [
                'eyebrow' => 'Quem somos',
                'title' => $old['about_title'] ?? 'Sobre a Soares Mudanças',
                'content' => $old['about_text'] ?? 'A Soares Mudanças oferece soluções completas para transporte de móveis, equipamentos e mudanças residenciais e comerciais. Nossa missão é proporcionar tranquilidade aos clientes, garantindo segurança, pontualidade e atendimento personalizado em cada serviço realizado.',
                'subtitle' => 'Por que escolher a Soares Mudanças?',
                'items' => $list($old['differentials'] ?? null, array_map(fn ($t) => ['text' => $t], [
                    'Equipe treinada e experiente', 'Atendimento personalizado', 'Veículos próprios',
                    'Pontualidade', 'Segurança no transporte', 'Excelente custo-benefício',
                ])),
            ]],
            ['steps', 'Como funciona', [
                'eyebrow' => 'Simples assim', 'title' => 'Como funciona',
                'items' => $list($old['steps'] ?? null, [
                    ['title' => 'Peça um orçamento', 'text' => 'Fale com a gente pelo WhatsApp ou pelo formulário.'],
                    ['title' => 'Planejamento', 'text' => 'Organizamos a logística, data, equipe e materiais.'],
                    ['title' => 'Execução', 'text' => 'Embalamos e transportamos com cuidado e pontualidade.'],
                    ['title' => 'Entrega', 'text' => 'Seus bens chegam ao destino com tranquilidade.'],
                ]),
            ]],
            ['gallery', 'Galeria de fotos', [
                'eyebrow' => 'Nosso trabalho', 'title' => 'Galeria',
            ]],
            ['testimonials', 'Depoimentos', [
                'eyebrow' => 'Quem já mudou com a gente', 'title' => 'O que nossos clientes dizem',
            ]],
            ['quote', 'Formulário de orçamento', [
                'eyebrow' => 'Orçamento grátis', 'title' => 'Conte sobre sua mudança',
                'subtitle' => 'Preencha o formulário e respondemos rapidinho. Prefere conversar? Chame no WhatsApp.',
                'button_text' => 'Enviar pedido de orçamento',
            ]],
            ['location', 'Localização', [
                'eyebrow' => 'Onde estamos', 'title' => 'Localização',
            ]],
        ];
    }

    /** Cria as seções que ainda não existem (idempotente). */
    public static function seed(array $oldSettings = []): void
    {
        foreach (self::definitions($oldSettings) as $i => [$key, $label, $data]) {
            if (DB::table('site_sections')->where('key', $key)->exists()) {
                continue;
            }
            if (isset($data['items'])) {
                $data['items'] = json_encode($data['items'], JSON_UNESCAPED_UNICODE);
            }
            DB::table('site_sections')->insert($data + [
                'key' => $key, 'label' => $label, 'is_active' => true, 'order' => $i + 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}
