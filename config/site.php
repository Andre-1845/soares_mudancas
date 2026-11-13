<?php

/*
| Valores padrão do site. Tudo aqui pode ser alterado pelo gestor em
| Admin → Configurações do site (o que for salvo lá tem prioridade).
*/

return [
    'defaults' => [
        // Identidade
        'company_name' => 'Soares Mudanças',
        'logo' => null,
        'favicon' => null,
        'primary_color' => '#0b2f5b',
        'accent_color' => '#c2410c',

        // Textos das seções (topo, sobre, etapas...) ficam em Admin → Seções da página (tabela site_sections).

        // Contato
        'whatsapp_number' => '5524998602998',
        'whatsapp_message' => 'Olá! Gostaria de um orçamento de mudança.',
        'whatsapp_greeting' => 'Olá! 👋 Como podemos ajudar na sua mudança?',
        'whatsapp_quick_replies' => [
            ['text' => 'Quero um orçamento de mudança'],
            ['text' => 'Mudança para outro estado'],
            ['text' => 'Preciso de guarda-móveis'],
            ['text' => 'Frete de poucos itens'],
        ],
        'phone' => '(24) 99860-2998',
        'email' => 'contato@soaresmudancas.com',
        'lead_notify_email' => 'contato@soaresmudancas.com',
        'address' => 'Resende - RJ',
        'address_city' => 'Resende',
        'address_state' => 'RJ',
        'postal_code' => null,
        'latitude' => null,
        'longitude' => null,
        'service_area' => 'Resende e região Sul Fluminense',
        'business_hours' => null,
        'cnpj' => null,
        'instagram' => null,
        'facebook' => null,
        'show_map' => true,

        // SEO
        'seo_title' => 'Soares Mudanças | Mudanças residenciais e comerciais em Resende - RJ',
        'seo_description' => 'Mudanças residenciais, comerciais e interestaduais em Resende e região. Embalagem, guarda-móveis e orçamento grátis pelo WhatsApp.',
        'og_image' => null,
        'google_analytics_id' => null,
    ],

    // Cloudflare Turnstile (captcha invisível, gratuito). Opcional: se vazio, o site usa só honeypot + limite de envios.
    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],
];
