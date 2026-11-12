<x-filament-panels::page>
    <div class="sm-cards">
        @foreach ($cards as $card)
            <a href="{{ $card['url'] }}" class="sm-card sm-card--{{ $card['tone'] }}">
                <span class="sm-card__icon">
                    <x-filament::icon :icon="$card['icon']" class="sm-card__svg" />
                </span>
                <span class="sm-card__body">
                    <span class="sm-card__title">{{ $card['title'] }}</span>
                    <span class="sm-card__text">{{ $card['text'] }}</span>
                    @if ($card['meta'])
                        <span class="sm-card__meta">{{ $card['meta'] }}</span>
                    @endif
                </span>
                <span class="sm-card__arrow" aria-hidden="true">→</span>
            </a>
        @endforeach
    </div>

    <div class="sm-tip">
        <strong>Dica:</strong> depois de salvar, abra o site em outra aba para conferir —
        <a href="{{ url('/') }}" target="_blank" rel="noopener">ver o site</a>.
    </div>
</x-filament-panels::page>
