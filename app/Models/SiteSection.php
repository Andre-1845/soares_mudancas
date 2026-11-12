<?php

namespace App\Models;

use App\Support\DefaultSections;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteSection extends Model
{
    protected $fillable = ['eyebrow', 'title', 'subtitle', 'content', 'button_text', 'items', 'is_active', 'order'];

    protected $casts = ['is_active' => 'boolean', 'items' => 'array'];

    public function images(): HasMany
    {
        return $this->hasMany(SiteImage::class)->orderBy('order')->orderBy('id');
    }

    public function anchor(): string
    {
        return DefaultSections::ANCHORS[$this->key] ?? $this->key;
    }

    public function acceptsImages(): bool
    {
        return in_array($this->key, DefaultSections::WITH_IMAGES, true);
    }

    /** Lista de itens sem as chaves internas do editor. */
    public function itemList(): array
    {
        return array_values(array_filter($this->items ?? [], fn ($i) => filled($i['text'] ?? $i['title'] ?? null)));
    }
}
