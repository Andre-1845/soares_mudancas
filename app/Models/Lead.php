<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Pedido de orçamento recebido pelo formulário do site. */
class Lead extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'service_type', 'origin', 'destination',
        'move_date', 'property_size', 'message', 'status', 'notes',
        'quoted_value', 'ip', 'user_agent',
    ];

    protected $casts = [
        'move_date' => 'date',
        'quoted_value' => 'decimal:2',
    ];

    public const STATUSES = [
        'novo' => 'Novo',
        'em_contato' => 'Em contato',
        'orcado' => 'Orçamento enviado',
        'fechado' => 'Fechado',
        'perdido' => 'Perdido',
    ];

    public const STATUS_COLORS = [
        'novo' => 'warning',
        'em_contato' => 'info',
        'orcado' => 'primary',
        'fechado' => 'success',
        'perdido' => 'gray',
    ];

    public const SERVICE_TYPES = [
        'residencial' => 'Mudança residencial',
        'comercial' => 'Mudança comercial',
        'interestadual' => 'Mudança interestadual',
        'guarda_moveis' => 'Guarda-móveis',
        'carreto' => 'Carreto / frete pequeno',
        'outro' => 'Outro',
    ];

    public const PROPERTY_SIZES = [
        'kitnet' => 'Kitnet / 1 cômodo',
        '1q' => '1 quarto',
        '2q' => '2 quartos',
        '3q' => '3 quartos',
        '4q' => '4+ quartos / casa grande',
        'escritorio' => 'Escritório / loja',
        'poucos_itens' => 'Poucos itens',
    ];

    /** Link para responder o cliente pelo WhatsApp a partir do admin. */
    public function whatsappUrl(): string
    {
        $digits = preg_replace('/\D/', '', $this->phone);
        if (strlen($digits) <= 11) {
            $digits = '55'.$digits;
        }
        $first = explode(' ', trim($this->name))[0];

        return 'https://wa.me/'.$digits.'?text='.rawurlencode("Olá {$first}! Aqui é da Soares Mudanças, recebemos seu pedido de orçamento.");
    }
}
