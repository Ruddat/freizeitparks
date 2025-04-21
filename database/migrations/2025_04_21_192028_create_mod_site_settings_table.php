<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mod_site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 255)->unique();
            $table->text('value')->nullable();
            $table->enum('type', ['string', 'file', 'json', 'boolean'])->default('string');
            $table->text('description')->nullable();
            $table->string('group')->default('general');
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });

        DB::table('mod_site_settings')->insert([
            // Allgemeine Einstellungen
            ['key' => 'site_name', 'value' => 'Parkverzeichnis.de', 'type' => 'string', 'description' => 'Name der Website', 'group' => 'general', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'logo', 'value' => '/storage/uploads/logo.png', 'type' => 'file', 'description' => 'Pfad zum Website-Logo', 'group' => 'general', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'owner_name', 'value' => 'Freizeitpark-Team', 'type' => 'string', 'description' => 'Name des Inhabers oder Betreiberteams', 'group' => 'general', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_email', 'value' => 'info@parkverzeichnis.de', 'type' => 'string', 'description' => 'Kontakt-E-Mail-Adresse', 'group' => 'general', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],

            // Social-Media
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/parkverzeichnis', 'type' => 'string', 'description' => 'Facebook-Seite', 'group' => 'social', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/parkverzeichnis', 'type' => 'string', 'description' => 'Instagram-Profil', 'group' => 'social', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'youtube_url', 'value' => 'https://youtube.com/@parkverzeichnis', 'type' => 'string', 'description' => 'YouTube-Kanal', 'group' => 'social', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tiktok_url', 'value' => 'https://www.tiktok.com/@parkverzeichnis', 'type' => 'string', 'description' => 'TikTok-Profil', 'group' => 'social', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],

            // SEO
            ['key' => 'default_meta_keywords', 'value' => json_encode(['freizeitparks', 'erlebnisparks', 'familienausflug']), 'type' => 'json', 'description' => 'Standard-SEO-Keywords', 'group' => 'seo', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'google_analytics_id', 'value' => 'G-XXXXXXX', 'type' => 'string', 'description' => 'Google Analytics ID', 'group' => 'seo', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],

            // Wartungsmodus
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'description' => 'Wartungsmodus aktivieren', 'group' => 'maintenance', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_message', 'value' => 'Parkverzeichnis wird gerade überarbeitet. Bitte schau später wieder vorbei!', 'type' => 'string', 'description' => 'Wartungsnachricht', 'group' => 'maintenance', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_start_at', 'value' => null, 'type' => 'string', 'description' => 'Startzeit Wartung (ISO 8601)', 'group' => 'maintenance', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_end_at', 'value' => null, 'type' => 'string', 'description' => 'Endzeit Wartung (ISO 8601)', 'group' => 'maintenance', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_allowed_ips', 'value' => json_encode(['127.0.0.1']), 'type' => 'json', 'description' => 'Erlaubte IPs bei Wartung', 'group' => 'maintenance', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],

            // Footer-Texte (rotierend)
            [
                'key' => 'footer_texts',
                'value' => json_encode([
                    'Entdecke alle Freizeitparks in deiner Nähe – schnell und übersichtlich!',
                    'Tägliche Updates zu Öffnungszeiten, Aktionen und Bewertungen.',
                    'Parkverzeichnis.de – dein Portal für Abenteuer, Spaß und Familienzeit.',
                    'Alle Parks. Eine Karte. Unendlich Spaß.',
                    'Von Achterbahn bis Streichelzoo – finde deinen perfekten Freizeitpark!',
                ]),
                'type' => 'json',
                'description' => 'Footer-Texte (drehend)',
                'group' => 'footer',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Freizeitpark-spezifisch
            ['key' => 'default_map_lat', 'value' => '51.1657', 'type' => 'string', 'description' => 'Standardkarte: Breitengrad', 'group' => 'parks', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'default_map_lng', 'value' => '10.4515', 'type' => 'string', 'description' => 'Standardkarte: Längengrad', 'group' => 'parks', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'default_map_zoom', 'value' => '6', 'type' => 'string', 'description' => 'Standardkarte: Zoom-Level', 'group' => 'parks', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'enable_radius_filter', 'value' => '1', 'type' => 'boolean', 'description' => 'Umkreis-Suche aktivieren', 'group' => 'parks', 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('mod_site_settings');
    }
};
