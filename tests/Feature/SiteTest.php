<?php

namespace Tests\Feature;

use App\Mail\LeadReceived;
use App\Models\Lead;
use App\Models\Setting;
use App\Models\SiteEvent;
use App\Models\SiteNotice;
use App\Models\SiteSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function validLead(array $over = []): array
    {
        return array_merge([
            'name' => 'Maria Silva',
            'phone' => '(24) 99999-1234',
            'email' => 'maria@example.com',
            'service_type' => 'residencial',
            'origin' => 'Resende',
            'destination' => 'Volta Redonda',
            'property_size' => '2q',
            'move_date' => now()->addWeek()->toDateString(),
            'message' => 'Tenho geladeira e sofá.',
            'consent' => '1',
        ], $over);
    }

    public function test_home_renders_with_contacts(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Soares Mudanças')
            ->assertSee('wa.me/5524998602998', false)
            ->assertSee('waze.com/ul', false)
            ->assertSee('"@type":"MovingCompany"', false)
            ->assertDontSee('O que nossos clientes dizem'); // depoimentos de exemplo estão desativados
    }

    public function test_structured_data_is_valid_json_ld(): void
    {
        Setting::setMany(['address' => '  Av. Brasil,  100 - Centro ', 'address_city' => 'Resende', 'postal_code' => '27500-000']);
        $html = $this->get('/')->assertOk()->getContent();

        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
        $data = json_decode($m[1] ?? '', true);

        $this->assertIsArray($data, 'JSON-LD inválido');
        $this->assertSame('https://schema.org', $data['@context']);
        $this->assertSame('MovingCompany', $data['@type']);
        $this->assertSame('Av. Brasil, 100 - Centro', $data['address']['streetAddress']);
        $this->assertSame('Resende', $data['address']['addressLocality']);
        $this->assertSame('27500-000', $data['address']['postalCode']);
        $this->assertStringContainsString('Av. Brasil, 100 - Centro, Resende - RJ, CEP 27500-000', $html);
    }

    public function test_lead_is_saved_and_email_sent(): void
    {
        Mail::fake();
        $this->postJson('/orcamento', $this->validLead())->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseHas('leads', ['name' => 'Maria Silva', 'status' => 'novo']);
        Mail::assertSent(LeadReceived::class, fn ($m) => $m->hasTo('contato@soaresmudancas.com'));
    }

    public function test_email_failure_does_not_lose_lead(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('smtp down'));
        $this->postJson('/orcamento', $this->validLead())->assertOk();
        $this->assertSame(1, Lead::count());
    }

    public function test_validation_and_honeypot(): void
    {
        $this->postJson('/orcamento', ['name' => '', 'phone' => 'abc'])
            ->assertStatus(422)->assertJsonValidationErrors(['name', 'phone', 'consent']);

        $this->postJson('/orcamento', $this->validLead(['website' => 'http://spam']))
            ->assertStatus(422);
        $this->assertSame(0, Lead::count());
    }

    public function test_rate_limit(): void
    {
        Mail::fake();
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/orcamento', $this->validLead())->assertOk();
        }
        $this->postJson('/orcamento', $this->validLead())->assertStatus(429);
    }

    public function test_click_tracking(): void
    {
        $this->post('/t', ['type' => 'whatsapp_click', 'source' => 'widget'])->assertNoContent();
        $this->post('/t', ['type' => 'hack'])->assertStatus(302);
        $this->assertSame(1, SiteEvent::count());
    }

    public function test_settings_change_site_content(): void
    {
        Setting::setMany(['whatsapp_number' => '5524911112222']);
        $this->get('/')->assertSee('wa.me/5524911112222', false);
    }

    public function test_sections_control_order_visibility_and_texts(): void
    {
        SiteSection::where('key', 'hero')->update(['title' => 'Mudança sem dor de cabeça']);
        SiteSection::where('key', 'steps')->update(['is_active' => false]);
        SiteSection::where('key', 'location')->update(['order' => 0]);

        $html = $this->get('/')->assertOk()
            ->assertSee('Mudança sem dor de cabeça')
            ->assertDontSee('id="como-funciona"', false)
            ->getContent();

        // localização passou para antes dos serviços
        $this->assertLessThan(strpos($html, 'id="servicos"'), strpos($html, 'id="localizacao"'));
    }

    public function test_gallery_appears_only_with_images(): void
    {
        $this->get('/')->assertDontSee('id="galeria"', false);
        SiteSection::where('key', 'gallery')->first()->images()->create(['path' => 'site/x.jpg', 'caption' => 'Caminhão']);
        $this->get('/')->assertSee('id="galeria"', false)->assertSee('storage/site/x.jpg', false);
    }

    public function test_notices_respect_period_and_bar(): void
    {
        SiteNotice::create(['title' => 'Promo de novembro', 'show_in_bar' => true, 'starts_at' => now()->subDay(), 'ends_at' => now()->addDays(5)]);
        SiteNotice::create(['title' => 'Aviso expirado', 'ends_at' => now()->subDay()]);
        SiteNotice::create(['title' => 'Aviso futuro', 'starts_at' => now()->addDays(3)]);

        $this->get('/')->assertSee('Promo de novembro')->assertSee('notice-bar', false)
            ->assertDontSee('Aviso expirado')->assertDontSee('Aviso futuro');
    }

    public function test_admin_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $this->actingAs(User::factory()->create());
        foreach (['/admin', '/admin/orcamentos', '/admin/servicos', '/admin/depoimentos', '/admin/conteudo-do-site', '/admin/secoes', '/admin/secoes/1/edit', '/admin/avisos', '/admin/avisos/create', '/admin/usuarios', '/admin/configuracoes-do-site'] as $url) {
            $this->get($url)->assertOk();
        }

        // widgets do painel (carregam via Livewire)
        foreach ([\App\Filament\Admin\Widgets\StatsOverview::class, \App\Filament\Admin\Widgets\LeadsChart::class, \App\Filament\Admin\Widgets\LatestLeads::class] as $w) {
            \Livewire\Livewire::test($w)->assertOk();
        }
        \Livewire\Livewire::test(\App\Filament\Admin\Pages\SiteSettings::class)
            ->set('data.phone', '(24) 3333-4444')
            ->call('save')->assertHasNoErrors();
        $this->assertSame('(24) 3333-4444', Setting::get('phone'));

        $about = SiteSection::where('key', 'about')->first();
        \Livewire\Livewire::test(\App\Filament\Admin\Resources\SiteSections\Pages\EditSiteSection::class, ['record' => $about->getRouteKey()])
            ->set('data.title', 'Quem somos nós')
            ->call('save')->assertHasNoErrors();
        $this->assertSame('Quem somos nós', $about->fresh()->title);
    }
}
