<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StaticPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Impressum',
                'slug' => 'impressum',
                'content' => <<<HTML
                    <h2>Angaben gemäß § 5 TMG</h2>
                    <p>Max Mustermann<br>Freizeitpark-Verzeichnis<br>Musterstraße 1<br>12345 Musterstadt</p>
                    <h3>Kontakt</h3>
                    <p>Telefon: 01234 567890<br>E-Mail: kontakt@freizeitparks.de</p>
                    <h3>Verantwortlich für den Inhalt</h3>
                    <p>Max Mustermann (Anschrift wie oben)</p>
                HTML,
                'show_in_footer' => true,
                'show_in_nav' => false,
            ],
            [
                'title' => 'Datenschutzerklärung',
                'slug' => 'datenschutz',
                'content' => <<<HTML
                    <h2>Datenschutzerklärung</h2>
                    <p>Wir nehmen den Schutz Ihrer persönlichen Daten ernst. Diese Website speichert keine unnötigen personenbezogenen Daten ohne Ihre Zustimmung.</p>
                    <h3>Cookies</h3>
                    <p>Unsere Seite verwendet nur technisch notwendige Cookies.</p>
                    <h3>Kontaktformular</h3>
                    <p>Wenn Sie uns über ein Formular kontaktieren, speichern wir Ihre Angaben nur zur Bearbeitung Ihrer Anfrage.</p>
                HTML,
                'show_in_footer' => true,
                'show_in_nav' => false,
            ],
            [
                'title' => 'Allgemeine Geschäftsbedingungen',
                'slug' => 'agb',
                'content' => <<<HTML
                    <h2>AGB</h2>
                    <p>Diese Allgemeinen Geschäftsbedingungen gelten für die Nutzung unserer Plattform durch Besucher und Parkbetreiber.</p>
                    <h3>1. Nutzung der Plattform</h3>
                    <p>Die Inhalte dienen der allgemeinen Information über Freizeitparks.</p>
                    <h3>2. Rechte & Pflichten</h3>
                    <p>Die Betreiber verpflichten sich, nur wahrheitsgemäße Angaben zu machen.</p>
                HTML,
                'show_in_footer' => true,
                'show_in_nav' => false,
            ],
            [
                'title' => 'API für Parkbetreiber',
                'slug' => 'api-parkbetreiber',
                'content' => <<<HTML
                    <h2>API für Parkbetreiber</h2>
                    <p>Sie können Ihre Parkdaten automatisiert über unsere REST-API aktualisieren.</p>
                    <h3>Basis-URL</h3>
                    <code>https://freizeitparks.de/api/v1/parks</code>
                    <h3>Authentifizierung</h3>
                    <p>Bitte verwenden Sie Ihren persönlichen API-Key im Header: <code>Authorization: Bearer &lt;API_KEY&gt;</code></p>
                    <h3>Beispiel: Öffnungszeiten aktualisieren</h3>
                    <pre><code>PATCH /api/v1/parks/123/opening-times</code></pre>
                HTML,
                'show_in_footer' => false,
                'show_in_nav' => true,
            ],
        ];

        foreach ($pages as $page) {
            StaticPage::updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
