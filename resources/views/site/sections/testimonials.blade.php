<section id="depoimentos" class="section section--soft">
    <div class="wrap">
        @include('site.partials.section-head')
        <div class="grid-cards">
            @foreach($testimonials as $t)
                <figure class="quote">
                    <div class="quote__stars" aria-label="{{ $t->rating }} de 5 estrelas">{{ str_repeat('★', $t->rating) }}<span>{{ str_repeat('★', 5 - $t->rating) }}</span></div>
                    <blockquote>“{{ $t->text }}”</blockquote>
                    <figcaption><strong>{{ $t->name }}</strong>@if($t->city) · {{ $t->city }}@endif @if($t->source)<small>via {{ $t->source }}</small>@endif</figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
