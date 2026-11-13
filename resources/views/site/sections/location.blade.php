<section id="localizacao" class="section">
    <div class="wrap location">
        <div>
            @if($section->eyebrow)<p class="eyebrow eyebrow--dark">{{ $section->eyebrow }}</p>@endif
            <h2>{{ $section->title }}</h2>
            @if($section->subtitle)<p>{{ $section->subtitle }}</p>@endif
            <p class="location__addr">📍 {{ $fullAddress }}</p>
            @if($s['business_hours'])<p>🕒 {{ $s['business_hours'] }}</p>@endif
            <p>Atendemos {{ $s['service_area'] }}.</p>
            <div class="location__btns">
                <a class="btn btn--waze" href="{{ $wazeLink }}" target="_blank" rel="noopener" data-track="waze_click" data-source="localizacao">
                    <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M12 2C6.9 2 3 5.7 3 10.3c0 1.7.5 3 1.2 4-.4.9-1.3 1.6-2.2 1.9.9 1 2.3 1.6 3.8 1.6.4 1.3 1.6 2.2 3 2.2 1.2 0 2.3-.7 2.8-1.7h1.8c.5 1 1.6 1.7 2.8 1.7 1.7 0 3.1-1.4 3.1-3.1v-.1C21.2 15.4 21 13 21 10.3 21 5.7 17.1 2 12 2zm-3 7.2a1.1 1.1 0 1 1 0 2.2 1.1 1.1 0 0 1 0-2.2zm6 0a1.1 1.1 0 1 1 0 2.2 1.1 1.1 0 0 1 0-2.2zM8.4 13h7.2c-.5 1.5-2 2.5-3.6 2.5S8.9 14.5 8.4 13z"/></svg>
                    Abrir no Waze
                </a>
                <a class="btn btn--ghost" href="{{ $mapsLink }}" target="_blank" rel="noopener" data-track="maps_click" data-source="localizacao">Ver no Google Maps</a>
            </div>
        </div>
        @if($s['show_map'])
            <div class="map">
                <iframe title="Mapa — {{ $name }}" src="{{ $mapEmbed }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            </div>
        @endif
    </div>
</section>
