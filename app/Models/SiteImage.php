<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SiteImage extends Model
{
    protected $fillable = ['path', 'caption', 'order'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(SiteSection::class, 'site_section_id');
    }

    public function url(): string
    {
        return asset('storage/'.ltrim($this->path, '/'));
    }

    protected static function booted(): void
    {
        // Ao remover a foto do painel, apaga o arquivo do servidor.
        static::deleted(fn (SiteImage $img) => Storage::disk('public')->delete($img->path));
    }
}
