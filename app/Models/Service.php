<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['title', 'icon', 'description', 'image', 'sort', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function scopeVisible(Builder $q): Builder
    {
        return $q->where('active', true)->orderBy('sort')->orderBy('id');
    }
}
