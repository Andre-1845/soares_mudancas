<section id="avisos" class="section section--soft">
    <div class="wrap">
        @include('site.partials.section-head')
        <div class="notices">
            @foreach ($notices as $n)
                <article class="notice notice--{{ $n->style }}">
                    <h3>{{ $n->title }}</h3>
                    @if ($n->text)<p>{!! nl2br(e($n->text)) !!}</p>@endif
                    <div class="notice__foot">
                        @if ($n->ends_at)<small>Válido até {{ $n->ends_at->format('d/m/Y') }}</small>@endif
                        @if ($n->link_url && $n->link_label)
                            <a class="btn btn--sm {{ $n->style === 'info' ? 'btn--primary' : 'btn--accent' }}" href="{{ $n->link_url }}" @if(str_starts_with($n->link_url, 'http')) target="_blank" rel="noopener" @endif>{{ $n->link_label }}</a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
