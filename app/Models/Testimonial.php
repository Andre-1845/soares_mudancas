<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'city', 'text', 'rating', 'source', 'sort', 'active'];

    protected $casts = ['active' => 'boolean', 'rating' => 'integer'];

    public function scopeVisible(Builder $q): Builder
    {
        return $q->where('active', true)->orderBy('sort')->orderByDesc('id');
    }
}
