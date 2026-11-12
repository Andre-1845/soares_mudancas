<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Mail\LeadReceived;
use App\Models\Lead;
use App\Models\Service;
use App\Models\Setting;
use App\Models\SiteEvent;
use App\Models\SiteNotice;
use App\Models\SiteSection;
use App\Support\DefaultSections;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SiteController extends Controller
{
    public function home()
    {
        $services = Service::visible()->get();
        $testimonials = Testimonial::visible()->get();
        $notices = SiteNotice::current()->get();

        $sections = SiteSection::with('images')->where('is_active', true)->orderBy('order')->get();

        // Seções que dependem de conteúdo só aparecem quando há o que mostrar.
        $visibleSections = $sections->filter(fn (SiteSection $sec) => match ($sec->key) {
            'services' => $services->isNotEmpty(),
            'testimonials' => $testimonials->isNotEmpty(),
            'notices' => $notices->isNotEmpty(),
            'gallery' => $sec->images->isNotEmpty(),
            'steps' => count($sec->itemList()) > 0,
            default => true,
        })->values();

        // Menu do topo montado a partir das seções visíveis, na mesma ordem.
        $menu = $visibleSections
            ->filter(fn ($sec) => isset(DefaultSections::MENU[$sec->key]))
            ->mapWithKeys(fn ($sec) => [$sec->anchor() => DefaultSections::MENU[$sec->key]])
            ->take(5);

        return view('site.home', [
            's' => Setting::allValues(),
            'services' => $services,
            'testimonials' => $testimonials,
            'notices' => $notices,
            'barNotice' => $notices->firstWhere('show_in_bar', true),
            'visibleSections' => $visibleSections,
            'quoteActive' => $visibleSections->contains('key', 'quote'),
            'menu' => $menu,
        ]);
    }

    public function storeLead(StoreLeadRequest $request)
    {
        $lead = Lead::create($request->safe()->except(['website', 'consent']) + [
            'status' => 'novo',
            'ip' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
        ]);

        // O pedido já está salvo no painel; se o e-mail falhar, não perdemos o cliente.
        try {
            if ($to = Setting::get('lead_notify_email')) {
                Mail::to($to)->send(new LeadReceived($lead));
            }
        } catch (\Throwable $e) {
            Log::error('Falha ao enviar e-mail do orçamento #'.$lead->id.': '.$e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Recebemos seu pedido! Entraremos em contato em breve.']);
        }

        return redirect()->to(route('home').'#orcamento')
            ->with('lead_ok', 'Recebemos seu pedido! Entraremos em contato em breve.');
    }

    /** Registra cliques (WhatsApp, Waze...) para as estatísticas do painel. */
    public function track(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:'.implode(',', array_keys(SiteEvent::TYPES))],
            'source' => ['nullable', 'alpha_dash', 'max:40'],
        ]);

        SiteEvent::create($data + ['ip_hash' => hash('sha256', $request->ip().config('app.key'))]);

        return response()->noContent();
    }

    public function sitemap()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            .'<url><loc>'.e(url('/')).'</loc><changefreq>weekly</changefreq><priority>1.0</priority></url>'
            .'</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
