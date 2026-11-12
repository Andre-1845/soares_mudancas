<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['type', 'source', 'ip_hash'];

    public const TYPES = [
        'whatsapp_click' => 'WhatsApp',
        'waze_click' => 'Waze',
        'maps_click' => 'Google Maps',
        'phone_click' => 'Telefone',
    ];
}
