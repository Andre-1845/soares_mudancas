<section id="galeria" class="section section--soft">
    <div class="wrap">
        @include('site.partials.section-head')
        <div class="gallery">
            @foreach($section->images as $g)
                <figure>
                    <img src="{{ $g->url() }}" alt="{{ $g->caption ?: 'Foto de mudança realizada' }}" loading="lazy">
                    @if($g->caption)<figcaption>{{ $g->caption }}</figcaption>@endif
                </figure>
            @endforeach
        </div>
    </div>
</section>
