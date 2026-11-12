<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SiteNotice extends Model
{
    protected $fillable = ['title', 'text', 'style', 'link_url', 'link_label', 'show_in_bar', 'starts_at', 'ends_at', 'is_active', 'order'];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_bar' => 'boolean',
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    public const STYLES = [
        'promo' => 'Promoção (laranja)',
        'info' => 'Informação (azul)',
        'alerta' => 'Atenção (vermelho)',
    ];

    /** Ativos e dentro do período (início/fim opcionais). */
    public function scopeCurrent(Builder $q): Builder
    {
        $today = now()->toDateString();

        return $q->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhereDate('starts_at', '<=', $today))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', $today))
            ->orderBy('order')->orderByDesc('id');
    }

    public function isCurrent(): bool
    {
        $today = now()->startOfDay();

        return $this->is_active
            && (! $this->starts_at || $this->starts_at->lte($today))
            && (! $this->ends_at || $this->ends_at->gte($today));
    }
}
