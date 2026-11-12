{{-- Cabeçalho padrão das seções: texto pequeno, título e subtítulo vindos do CMS. --}}
<header class="section__head">
    @if ($section->eyebrow)<p class="eyebrow {{ ($dark ?? false) ? '' : 'eyebrow--dark' }}">{{ $section->eyebrow }}</p>@endif
    <h2>{{ $section->title }}</h2>
    @if ($section->subtitle)<p class="section__sub">{{ $section->subtitle }}</p>@endif
</header>
