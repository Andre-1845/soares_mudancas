@php
    use App\Models\Lead;
    $img = fn ($p) => $p ? asset('storage/'.ltrim($p, '/')) : null;
    $name = $s['company_name'];
    $wa = preg_replace('/\D/', '', (string) $s['whatsapp_number']);
    $waLink = 'https://wa.me/'.$wa.'?text='.rawurlencode((string) $s['whatsapp_message']);
    $phoneDigits = preg_replace('/\D/', '', (string) $s['phone']);
    $lat = $s['latitude']; $lng = $s['longitude'];
    $hasCoords = filled($lat) && filled($lng);
    $fullAddress = \App\Support\SeoSchema::fullAddress($s);
    $placeQuery = $hasCoords ? "$lat,$lng" : trim($name.' '.$fullAddress);
    $wazeLink = $hasCoords
        ? "https://waze.com/ul?ll={$lat},{$lng}&navigate=yes"
        : 'https://waze.com/ul?q='.rawurlencode($fullAddress).'&navigate=yes';
    $mapsLink = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($placeQuery);
    $mapEmbed = 'https://www.google.com/maps?q='.rawurlencode($hasCoords ? "$lat,$lng" : $fullAddress).'&z='.($hasCoords ? 16 : 12).'&output=embed';
    $heroFirst = optional($visibleSections->firstWhere('key', 'hero'))->images?->first();
    $ogImg = $img($s['og_image']) ?? $heroFirst?->url();
    $quick = collect($s['whatsapp_quick_replies'] ?? [])->pluck('text')->filter();
    $v = fn ($f) => asset($f).'?v='.@filemtime(public_path($f));
    $turnstile = config('site.turnstile.site_key');
@endphp
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $s['seo_title'] }}</title>
    <meta name="description" content="{{ $s['seo_description'] }}">
    <link rel="canonical" href="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="{{ $s['primary_color'] }}">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="{{ $name }}">
    <meta property="og:title" content="{{ $s['seo_title'] }}">
    <meta property="og:description" content="{{ $s['seo_description'] }}">
    <meta property="og:url" content="{{ url('/') }}">
    @if($ogImg)<meta property="og:image" content="{{ $ogImg }}">@endif
    <meta name="twitter:card" content="summary_large_image">

    @if($s['favicon'])
        <link rel="icon" href="{{ $img($s['favicon']) }}">
    @else
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🚚</text></svg>">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ $v('css/site.css') }}">
    <style>:root{--primary: {{ $s['primary_color'] }}; --accent: {{ $s['accent_color'] }};}</style>

    <script type="application/ld+json">
    {!! json_encode(\App\Support\SeoSchema::movingCompany($s, $ogImg, $img($s['logo'])), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}
    </script>

    @if($s['google_analytics_id'])
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $s['google_analytics_id'] }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config',@json($s['google_analytics_id']));</script>
    @endif
    @if($turnstile)
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
</head>
<body>
<a class="skip" href="#conteudo">Pular para o conteúdo</a>

@if ($barNotice)
    <div class="notice-bar notice-bar--{{ $barNotice->style }}" role="region" aria-label="Aviso">
        <div class="wrap notice-bar__in">
            <strong>{{ $barNotice->title }}</strong>
            @if ($barNotice->link_url && $barNotice->link_label)
                <a href="{{ $barNotice->link_url }}" @if(str_starts_with($barNotice->link_url, 'http')) target="_blank" rel="noopener" @endif>{{ $barNotice->link_label }} →</a>
            @endif
        </div>
    </div>
@endif

<header class="topbar" id="topo">
    <div class="wrap topbar__in">
        <a href="#topo" class="brand" aria-label="{{ $name }} — início">
            @if($s['logo'])
                <img src="{{ $img($s['logo']) }}" alt="{{ $name }}" height="44">
            @else
                <span class="brand__mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17h4V5H2v12h3"/><path d="M20 17h2v-3.34a4 4 0 0 0-1.17-2.83L19 9h-5v8h1"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                </span>
                <span class="brand__name">{{ $name }}</span>
            @endif
        </a>
        <button class="menu-btn" aria-expanded="false" aria-controls="menu" aria-label="Abrir menu">
            <span></span><span></span><span></span>
        </button>
        <nav id="menu" class="menu" aria-label="Principal">
            @foreach ($menu as $anchor => $label)
                <a href="#{{ $anchor }}">{{ $label }}</a>
            @endforeach
            @if ($quoteActive)
                <a href="#orcamento" class="btn btn--accent btn--sm">Orçamento grátis</a>
            @endif
        </nav>
    </div>
</header>

<main id="conteudo">
    @foreach ($visibleSections as $section)
        @include('site.sections.'.$section->key)
    @endforeach
</main>

<footer class="footer">
    <div class="wrap footer__in">
        <div>
            <strong class="footer__brand">{{ $name }}</strong>
            <p>{{ $s['service_area'] }}</p>
            @if($s['cnpj'])<p>CNPJ {{ $s['cnpj'] }}</p>@endif
        </div>
        <div>
            <p><a href="{{ $waLink }}" class="js-wa-open" data-track="whatsapp_click" data-source="rodape" target="_blank" rel="noopener">WhatsApp {{ $s['phone'] }}</a></p>
            @if($s['email'])<p>@if($quoteActive)<a href="#orcamento">{{ $s['email'] }}</a>@else{{ $s['email'] }}@endif</p>@endif
            <p>{{ $fullAddress }}</p>
        </div>
        @if($s['instagram'] || $s['facebook'])
        <div class="footer__social">
            @if($s['instagram'])<a href="{{ $s['instagram'] }}" target="_blank" rel="noopener">Instagram</a>@endif
            @if($s['facebook'])<a href="{{ $s['facebook'] }}" target="_blank" rel="noopener">Facebook</a>@endif
        </div>
        @endif
    </div>
    <p class="wrap footer__copy">© {{ date('Y') }} {{ $name }}. Todos os direitos reservados.</p>
</footer>

{{-- WIDGET WHATSAPP --}}
<div class="wa" data-number="{{ $wa }}">
    <div class="wa__panel" id="wa-panel" role="dialog" aria-modal="false" aria-labelledby="wa-title" hidden>
        <div class="wa__head">
            <span class="wa__avatar" aria-hidden="true">@if($s['logo'])<img src="{{ $img($s['logo']) }}" alt="">@else 🚚 @endif</span>
            <div>
                <strong id="wa-title">{{ $name }}</strong>
                <small>Normalmente responde em poucos minutos</small>
            </div>
            <button class="wa__close" type="button" aria-label="Fechar">×</button>
        </div>
        <div class="wa__body">
            <div class="wa__bubble">{{ $s['whatsapp_greeting'] }}</div>
            @if($quick->isNotEmpty())
                <div class="wa__chips">
                    @foreach($quick as $q)<button type="button" class="wa__chip">{{ $q }}</button>@endforeach
                </div>
            @endif
        </div>
        <form class="wa__form">
            <label class="sr-only" for="wa-msg">Sua mensagem</label>
            <textarea id="wa-msg" rows="2" maxlength="600" placeholder="Digite sua mensagem...">{{ $s['whatsapp_message'] }}</textarea>
            <button type="submit" aria-label="Iniciar conversa no WhatsApp">
                <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path fill="currentColor" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
            </button>
        </form>
    </div>
    <button class="wa__fab" type="button" aria-controls="wa-panel" aria-expanded="false" aria-label="Conversar no WhatsApp">
        @include('site.partials.wa-icon', ['size' => 30])
    </button>
</div>

<script src="{{ $v('js/site.js') }}" defer></script>
</body>
</html>
