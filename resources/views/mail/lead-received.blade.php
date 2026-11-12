<x-mail::message>
# Novo pedido de orçamento

**Nome:** {{ $lead->name }}
**Telefone/WhatsApp:** {{ $lead->phone }}
@if($lead->email)
**E-mail:** {{ $lead->email }}
@endif
@if($lead->service_type)
**Serviço:** {{ \App\Models\Lead::SERVICE_TYPES[$lead->service_type] ?? $lead->service_type }}
@endif
@if($lead->property_size)
**Tamanho:** {{ \App\Models\Lead::PROPERTY_SIZES[$lead->property_size] ?? $lead->property_size }}
@endif
@if($lead->origin)
**Origem:** {{ $lead->origin }}
@endif
@if($lead->destination)
**Destino:** {{ $lead->destination }}
@endif
@if($lead->move_date)
**Data desejada:** {{ $lead->move_date->format('d/m/Y') }}
@endif

@if($lead->message)
**Mensagem:**
{{ $lead->message }}
@endif

<x-mail::button :url="$lead->whatsappUrl()" color="success">
Responder pelo WhatsApp
</x-mail::button>

<x-mail::button :url="\App\Filament\Admin\Resources\Leads\LeadResource::getUrl('edit', ['record' => $lead])">
Abrir no painel
</x-mail::button>

Recebido em {{ $lead->created_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}.
</x-mail::message>
