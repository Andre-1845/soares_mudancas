<section id="servicos" class="section">
    <div class="wrap">
        @include('site.partials.section-head')
        <div class="grid-cards">
            @foreach($services as $service)
                <article class="card">
                    @if($service->image)
                        <img class="card__img" src="{{ $img($service->image) }}" alt="{{ $service->title }}" loading="lazy">
                    @elseif($service->icon)
                        <span class="card__icon" aria-hidden="true">{{ $service->icon }}</span>
                    @endif
                    <h3>{{ $service->title }}</h3>
                    @if($service->description)<p>{{ $service->description }}</p>@endif
                </article>
            @endforeach
        </div>
    </div>
</section>
