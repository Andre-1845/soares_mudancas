<section id="sobre" class="section section--dark">
    <div class="wrap about">
        <div>
            @if($section->eyebrow)<p class="eyebrow">{{ $section->eyebrow }}</p>@endif
            <h2>{{ $section->title }}</h2>
            @foreach (preg_split('/\R{2,}/', trim((string) $section->content)) as $para)
                @if (trim($para) !== '')<p class="about__text">{!! nl2br(e(trim($para))) !!}</p>@endif
            @endforeach
            @if($aboutImg = $section->images->first())
                <img class="about__img" src="{{ $aboutImg->url() }}" alt="{{ $aboutImg->caption ?: 'Equipe '.$name }}" loading="lazy">
            @endif
        </div>
        @php $differentials = collect($section->itemList())->pluck('text')->filter(); @endphp
        @if($differentials->isNotEmpty())
        <div class="diffs">
            @if($section->subtitle)<h3>{{ $section->subtitle }}</h3>@endif
            <ul>
                @foreach($differentials as $d)
                    <li><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M5 12.5l4.5 4.5L19 7.5"/></svg>{{ $d }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</section>
