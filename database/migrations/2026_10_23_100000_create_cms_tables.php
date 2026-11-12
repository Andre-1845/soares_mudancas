<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * CMS por seções (inspirado no projeto dízimo):
 * - site_sections: cada bloco da página inicial (ordem, ligado/desligado, textos, listas)
 * - site_images: fotos ligadas a uma seção (topo, sobre, galeria...)
 * - site_notices: avisos e promoções com período de exibição
 * Migra os textos que estavam em settings e as fotos da antiga galeria.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key', 40)->unique();
            $table->string('label', 80);
            $table->string('eyebrow', 120)->nullable();
            $table->string('title', 160)->nullable();
            $table->string('subtitle', 300)->nullable();
            $table->text('content')->nullable();
            $table->string('button_text', 60)->nullable();
            $table->json('items')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('site_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_section_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('caption', 160)->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('site_notices', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->text('text')->nullable();
            $table->string('style', 20)->default('promo'); // promo | info | alerta
            $table->string('link_url')->nullable();
            $table->string('link_label', 40)->nullable();
            $table->boolean('show_in_bar')->default(false);
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        \App\Support\DefaultSections::seed(
            Schema::hasTable('settings') ? DB::table('settings')->pluck('value', 'key')->all() : []
        );

        // Fotos da antiga galeria → imagens da seção "gallery"
        if (Schema::hasTable('gallery_images')) {
            $galleryId = DB::table('site_sections')->where('key', 'gallery')->value('id');
            foreach (DB::table('gallery_images')->orderBy('sort')->get() as $g) {
                DB::table('site_images')->insert([
                    'site_section_id' => $galleryId, 'path' => $g->image, 'caption' => $g->caption,
                    'order' => $g->sort, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            Schema::drop('gallery_images');
        }

        // Foto do topo/sobre que estavam nas configurações
        foreach (['hero_image' => 'hero', 'about_image' => 'about'] as $settingKey => $sectionKey) {
            $path = Schema::hasTable('settings') ? DB::table('settings')->where('key', $settingKey)->value('value') : null;
            if ($path) {
                DB::table('site_images')->insert([
                    'site_section_id' => DB::table('site_sections')->where('key', $sectionKey)->value('id'),
                    'path' => $path, 'order' => 0, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_images');
        Schema::dropIfExists('site_sections');
        Schema::dropIfExists('site_notices');
    }
};
