<section id="orcamento" class="section section--accent">
    <div class="wrap quote-wrap">
        <div class="quote-intro">
            @if($section->eyebrow)<p class="eyebrow eyebrow--dark">{{ $section->eyebrow }}</p>@endif
            <h2>{{ $section->title }}</h2>
            @if($section->subtitle)<p>{{ $section->subtitle }}</p>@endif
            <ul class="contact-list">
                <li><a href="{{ $waLink }}" class="js-wa-open" data-track="whatsapp_click" data-source="orcamento" target="_blank" rel="noopener">@include('site.partials.wa-icon') WhatsApp {{ $s['phone'] }}</a></li>
                @if($phoneDigits)<li><a href="tel:+55{{ substr($phoneDigits, -11) }}" data-track="phone_click" data-source="orcamento">📞 Ligar: {{ $s['phone'] }}</a></li>@endif
                @if($s['email'])<li>✉️ {{ $s['email'] }}</li>@endif
            </ul>
        </div>

        <form class="form" method="POST" action="{{ route('lead.store') }}" novalidate id="lead-form">
            @csrf
            @if(session('lead_ok'))
                <div class="alert alert--ok" role="status">{{ session('lead_ok') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert--err" role="alert">Confira os campos destacados.</div>
            @endif
            <div class="form__status" role="status" aria-live="polite"></div>

            <div class="hp" aria-hidden="true">
                <label>Não preencha <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
            </div>

            <div class="row">
                <label class="field">Nome *
                    <input name="name" required maxlength="120" autocomplete="name" value="{{ old('name') }}">
                    <small class="err" data-for="name">{{ $errors->first('name') }}</small>
                </label>
                <label class="field">WhatsApp / telefone *
                    <input name="phone" required maxlength="30" inputmode="tel" autocomplete="tel" placeholder="(24) 9 0000-0000" value="{{ old('phone') }}">
                    <small class="err" data-for="phone">{{ $errors->first('phone') }}</small>
                </label>
            </div>
            <div class="row">
                <label class="field"><span>E-mail <span class="opt">(opcional)</span></span>
                    <input type="email" name="email" maxlength="150" autocomplete="email" value="{{ old('email') }}">
                    <small class="err" data-for="email">{{ $errors->first('email') }}</small>
                </label>
                <label class="field">Tipo de serviço
                    <select name="service_type">
                        <option value="">Selecione</option>
                        @foreach(\App\Models\Lead::SERVICE_TYPES as $k => $label)
                            <option value="{{ $k }}" @selected(old('service_type') === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="row">
                <label class="field">Saindo de (cidade/bairro)
                    <input name="origin" maxlength="150" value="{{ old('origin') }}">
                </label>
                <label class="field">Indo para (cidade/bairro)
                    <input name="destination" maxlength="150" value="{{ old('destination') }}">
                </label>
            </div>
            <div class="row">
                <label class="field">Tamanho do imóvel
                    <select name="property_size">
                        <option value="">Selecione</option>
                        @foreach(\App\Models\Lead::PROPERTY_SIZES as $k => $label)
                            <option value="{{ $k }}" @selected(old('property_size') === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="field">Data desejada
                    <input type="date" name="move_date" min="{{ now()->toDateString() }}" value="{{ old('move_date') }}">
                    <small class="err" data-for="move_date">{{ $errors->first('move_date') }}</small>
                </label>
            </div>
            <label class="field"><span>Detalhes <span class="opt">(itens grandes, escadas, elevador...)</span></span>
                <textarea name="message" rows="3" maxlength="2000">{{ old('message') }}</textarea>
            </label>
            <label class="check">
                <input type="checkbox" name="consent" value="1" required @checked(old('consent'))>
                <span>Autorizo a {{ $name }} a entrar em contato comigo sobre este orçamento.</span>
            </label>
            <small class="err" data-for="consent">{{ $errors->first('consent') }}</small>
            @if($turnstile)
                <div class="cf-turnstile" data-sitekey="{{ $turnstile }}" data-language="pt-br"></div>
                <small class="err" data-for="captcha">{{ $errors->first('captcha') }}</small>
            @endif
            <button type="submit" class="btn btn--primary btn--lg btn--block">{{ $section->button_text ?: 'Enviar pedido de orçamento' }}</button>
        </form>
    </div>
</section>
