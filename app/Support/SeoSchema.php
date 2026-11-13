<?php

namespace App\Support;

/**
 * Dados estruturados (Schema.org) da empresa para o Google.
 *
 * Montado em PHP e não no Blade de propósito: no Blade, "@context" é lido como
 * a diretiva @context do Laravel e corrompia o JSON-LD.
 */
class SeoSchema
{
    /** Endereço completo para exibir, Waze e mapa (não repete cidade/CEP já digitados na rua). */
    public static function fullAddress(array $s): string
    {
        $street = trim(preg_replace('/\s+/u', ' ', (string) ($s['address'] ?? '')));
        $parts = [$street];
        $city = trim(($s['address_city'] ?? '').' - '.($s['address_state'] ?? ''), ' -');
        if ($city !== '' && ! str_contains(mb_strtolower($street), mb_strtolower((string) ($s['address_city'] ?? '#')))) {
            $parts[] = $city;
        }
        if (! empty($s['postal_code']) && ! str_contains($street, (string) $s['postal_code'])) {
            $parts[] = 'CEP '.$s['postal_code'];
        }

        return implode(', ', array_filter($parts));
    }

    public static function movingCompany(array $s, ?string $image = null, ?string $logo = null): array
    {
        $clean = fn ($v) => is_string($v) ? trim(preg_replace('/\s+/u', ' ', $v)) : $v;
        $phone = preg_replace('/\D/', '', (string) $s['phone']);
        $lat = $s['latitude'] ?? null;
        $lng = $s['longitude'] ?? null;
        $hasCoords = filled($lat) && filled($lng);

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'MovingCompany',
            '@id' => url('/').'#empresa',
            'name' => $s['company_name'],
            'url' => url('/'),
            'telephone' => $phone ? '+55'.substr($phone, -11) : null,
            'email' => $s['email'] ?: null,
            'image' => $image,
            'logo' => $logo,
            'description' => $s['seo_description'],
            'areaServed' => $clean($s['service_area']),
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $clean($s['address']),
                'addressLocality' => $clean($s['address_city'] ?? null),
                'postalCode' => $clean($s['postal_code'] ?? null),
                'addressRegion' => $clean($s['address_state'] ?? 'RJ'),
                'addressCountry' => 'BR',
            ]),
            'geo' => $hasCoords ? ['@type' => 'GeoCoordinates', 'latitude' => (float) $lat, 'longitude' => (float) $lng] : null,
            'hasMap' => 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($hasCoords ? "$lat,$lng" : (string) $s['address']),
            'sameAs' => array_values(array_filter([$s['instagram'] ?? null, $s['facebook'] ?? null])) ?: null,
        ];

        return array_filter($schema, fn ($v) => $v !== null && $v !== '');
    }
}
