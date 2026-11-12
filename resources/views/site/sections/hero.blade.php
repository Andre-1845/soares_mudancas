@php $heroImages = $section->images; @endphp
<section id="topo-banner" class="hero {{ $heroImages->isNotEmpty() ? 'hero--photo' : '' }}">
    @if ($heroImages->isNotEmpty())
        <div class="hero__slides" aria-hidden="true">
            @foreach ($heroImages as $im)
                <div class="hero__slide {{ $loop->first ? 'is-on' : '' }}" style="background-image:url('{{ $im->url() }}')"></div>
            @endforeach
        </div>
    @endif
    <div class="wrap hero__in">
        @if ($section->eyebrow)<p class="eyebrow">{{ $section->eyebrow }}</p>@endif
        <h1>{{ $section->title }}</h1>
        @if ($section->subtitle)<p class="hero__sub">{{ $section->subtitle }}</p>@endif
        <div class="hero__cta">
            @if ($quoteActive)
                <a href="#orcamento" class="btn btn--accent btn--lg">{{ $section->button_text ?: 'Pedir orçamento' }}</a>
            @endif
            <a href="{{ $waLink }}" class="btn btn--wa btn--lg js-wa-open" data-track="whatsapp_click" data-source="hero" target="_blank" rel="noopener">
                @include('site.partials.wa-icon') Falar no WhatsApp
            </a>
        </div>
        @if ($items = $section->itemList())
            <ul class="trust" aria-label="Destaques">
                @foreach ($items as $it)<li>{{ $it['text'] }}</li>@endforeach
            </ul>
        @endif
    </div>
</section>
