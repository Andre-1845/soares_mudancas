<section id="como-funciona" class="section">
    <div class="wrap">
        @include('site.partials.section-head')
        <ol class="steps">
            @foreach($section->itemList() as $step)
                <li>
                    <span class="steps__n">{{ $loop->iteration }}</span>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['text'] ?? '' }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
