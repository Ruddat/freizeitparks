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
                'content' => <<<'HTML'
            <h2>Impressum</h2>

            <p>Angaben gemäß § 5 TMG</p>
            <p>
                Ingo Ruddat<br>
                Heidkrugsweg 31<br>
                31234 Edemissen
            </p>

            <h3>Kontakt</h3>
            <p>
                Website: <a href="https://ruddattech.de" target="_blank">https://ruddattech.de</a><br>
                <div class="my-6">
                        <button onclick="window.dispatchEvent(new CustomEvent('openContactOverlay'))" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">
                        ✉️ Kontakt aufnehmen
                        </button>
                        </div>
            </p>

            <h3>Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h3>
            <p>
                Ingo Ruddat<br>
                Heidkrugsweg 31<br>
                31234 Edemissen
            </p>

            <h3>Haftung für Inhalte</h3>
            <p>
                Als Diensteanbieter sind wir gemäß § 7 Abs. 1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich.
                Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen.
            </p>
            <p>
                Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich.
                Bei Bekanntwerden entsprechender Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.
            </p>

            <h3>Haftung für Links</h3>
            <p>
                Unser Angebot enthält Links zu externen Webseiten Dritter, auf deren Inhalte wir keinen Einfluss haben.
                Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich.
            </p>

            <h3>Urheberrecht</h3>
            <p>
                Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht.
                Beiträge Dritter sind als solche gekennzeichnet. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers.
            </p>
            <p>
                Teile der Bildinhalte stammen aus frei verwendbaren Quellen, insbesondere von <a href="https://unsplash.com" target="_blank">Unsplash</a> und <a href="https://pixabay.com" target="_blank">Pixabay</a>. Diese Bilder werden gemäß den jeweiligen Lizenzbedingungen verwendet.
            </p>

            <h3>Streitschlichtung</h3>
            <p>
                Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit:
                <a href="https://ec.europa.eu/consumers/odr/" target="_blank">https://ec.europa.eu/consumers/odr/</a>.
            </p>
            <p>
                Wir sind nicht verpflichtet und nicht bereit, an einem Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.
            </p>

            <h3>Hinweis bei Rechtsverstößen</h3>
            <p>
                Sollten Sie der Ansicht sein, dass auf dieser Website Inhalte Ihre Rechte verletzen oder gegen geltendes Recht verstoßen, teilen Sie uns dies bitte formlos unter <a href="mailto:info@parkverzeichnis.de">info@parkverzeichnis.de</a> mit. Wir prüfen jede Mitteilung sorgfältig und entfernen rechtswidrige Inhalte unverzüglich nach Bekanntwerden.
            </p>
            HTML,
                'show_in_footer' => true,
                'show_in_nav' => false,
            ],

            [
                'title' => 'Datenschutzerklärung',
                'slug' => 'datenschutz',
                'content' => <<<'HTML'
                    <h2>Datenschutzerklärung</h2>
                    <p>Der Schutz Ihrer persönlichen Daten ist uns wichtig. Wir verarbeiten Ihre Daten ausschließlich auf Grundlage der gesetzlichen Bestimmungen (DSGVO, TMG).</p>

                    <h3>1. Verantwortlicher</h3>
                    <p>Verantwortlich für die Datenverarbeitung auf dieser Website ist:<br>
                    <strong>Ingo Ruddat<br>Heidkrugsweg 31<br>31234 Edemissen</strong><br>
                    <div class="my-6">
                        <button onclick="window.dispatchEvent(new CustomEvent('openContactOverlay'))" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">
                        ✉️ Kontakt aufnehmen
                        </button>
                        </div>
                    </p>

                    <h3>2. Newsletter</h3>
                    <p>Wenn Sie sich für unseren Newsletter anmelden, speichern wir Ihre E-Mail-Adresse zum Zweck der regelmäßigen Zusendung von Informationen. Die Verarbeitung erfolgt auf Grundlage Ihrer Einwilligung gemäß Art. 6 Abs. 1 lit. a DSGVO.</p>
                    <p>Sie können den Newsletter jederzeit abbestellen – entweder über den Abmeldelink in jeder E-Mail oder durch eine formlose Mitteilung an uns. Ihre Daten werden nach Widerruf gelöscht, sofern keine gesetzlichen Aufbewahrungspflichten bestehen.</p>

                    <h3>3. Kontaktaufnahme</h3>
                    <p>Wenn Sie uns über ein Formular oder per E-Mail kontaktieren, verwenden wir Ihre Daten nur zur Bearbeitung Ihrer Anfrage. Diese Daten werden ohne Ihre ausdrückliche Zustimmung nicht weitergegeben.</p>

                    <h3>4. Zugriffsdaten</h3>
                    <p>Beim Besuch dieser Website werden technische Daten (z. B. IP-Adresse, Zeitpunkt, Browser) automatisch erfasst. Diese Daten dienen ausschließlich der Betriebssicherheit und Fehleranalyse und werden nicht zur Identifikation verwendet.</p>

                    <h3>5. Cookies</h3>
                    <p>Wir verwenden nur technisch notwendige Cookies, um die grundlegende Funktionalität der Website sicherzustellen. Es erfolgt kein Tracking und keine Profilbildung.</p>

                    <h3>6. Ihre Rechte</h3>
                    <p>Sie haben jederzeit das Recht auf Auskunft, Berichtigung, Löschung, Einschränkung der Verarbeitung, Datenübertragbarkeit sowie Widerspruch gegen die Verarbeitung. Wenden Sie sich dazu an uns unter den oben genannten Kontaktdaten.</p>

                    <h3>7. Änderungen dieser Erklärung</h3>
                    <p>Wir behalten uns vor, diese Datenschutzerklärung anzupassen, sofern sich gesetzliche Grundlagen oder unser Angebot ändern.</p>
                HTML,
                'show_in_footer' => true,
                'show_in_nav' => false,
            ],
            [
                'title' => 'Allgemeine Geschäftsbedingungen',
                'slug' => 'agb',
                'content' => <<<'HTML'
            <h2>Allgemeine Geschäftsbedingungen (AGB)</h2>

            <p>
                Diese AGB regeln die Nutzung der Plattform <strong>parkverzeichnis.de</strong> für alle Besucher:innen sowie eingetragene Parkbetreiber. Mit dem Zugriff auf die Website oder der Nutzung angebotener Funktionen erklären Sie sich mit diesen Bedingungen einverstanden.
            </p>

            <h3>1. Ziel der Plattform</h3>
            <p>
                <strong>parkverzeichnis.de</strong> bietet ein zentrales Verzeichnis für Freizeitparks, Zoos, Wasserparks und ähnliche Einrichtungen in Deutschland und Europa. Nutzer:innen können Parks entdecken, bewerten und vergleichen. Parkbetreiber haben die Möglichkeit, ihre Informationen selbst zu verwalten und über eine API aktuell zu halten.
            </p>

            <h3>2. Nutzung durch Besucher:innen</h3>
            <ul>
                <li>Die Nutzung ist kostenlos und ohne Registrierung möglich.</li>
                <li>Bewertungen, Kommentare oder Feedback dürfen nur ehrlich und sachlich erfolgen.</li>
                <li>Die kommerzielle Nutzung oder automatisierte Datenerfassung ist untersagt.</li>
            </ul>

            <h3>3. Nutzung durch Parkbetreiber</h3>
            <ul>
                <li>Parkbetreiber verpflichten sich, nur korrekte, aktuelle und relevante Daten zu hinterlegen.</li>
                <li>Verlinkungen oder Medieninhalte müssen den geltenden Gesetzen entsprechen.</li>
                <li>Ein API-Zugang darf nicht weitergegeben oder missbräuchlich verwendet werden.</li>
            </ul>

            <h3>4. Inhalte & Haftung</h3>
            <p>
                Die bereitgestellten Inhalte dienen ausschließlich der Information. Trotz sorgfältiger Pflege können wir keine Garantie für Vollständigkeit, Richtigkeit oder jederzeitige Verfügbarkeit übernehmen. Externe Links unterliegen der Verantwortung des jeweiligen Betreibers.
            </p>

            <h3>5. Änderungen & Updates</h3>
            <p>
                Wir behalten uns das Recht vor, Funktionen, Inhalte oder diese AGB jederzeit anzupassen. Nutzer:innen werden bei wesentlichen Änderungen entsprechend informiert.
            </p>

            <h3>6. Datenschutz</h3>
            <p>
                Informationen zur Verarbeitung personenbezogener Daten finden Sie in unserer <a href="/datenschutz">Datenschutzerklärung</a>.
            </p>

            <h3>7. Schlussbestimmungen</h3>
            <p>
                Es gilt deutsches Recht. Gerichtsstand ist – soweit gesetzlich zulässig – der Sitz des Plattformbetreibers.
            </p>
            HTML,
                'show_in_footer' => true,
                'show_in_nav' => false,
            ],

            [
                'title' => 'API für Parkbetreiber',
                'slug' => 'api-parkbetreiber',
                'content' => <<<'HTML'
                    <h2>API für Parkbetreiber</h2>
                    <p>
                        Sie betreiben einen Freizeitpark, Zoo oder Wasserpark und möchten Ihre Daten auf <strong>parkverzeichnis.de</strong> stets aktuell halten? Kein Problem!
                        Mit unserer <strong>REST-API</strong> können Sie wichtige Informationen wie <em>Öffnungszeiten, Wartungsinfos, Warteschlangenzeiten</em> oder <em>temporäre Schließungen</em> ganz einfach automatisiert übertragen.
                    </p>

                    <h3>Vorteile der Integration</h3>
                    <ul>
                        <li>📅 Öffnungszeiten tagesaktuell synchronisieren</li>
                        <li>⏱ Live-Wartezeiten anzeigen (für Besucher besonders wertvoll!)</li>
                        <li>📢 Temporäre Events, Schließungen oder Hinweise kommunizieren</li>
                        <li>🔄 Regelmäßige Datenpflege ohne manuelles Einloggen</li>
                    </ul>

                    <h3>Basis-Endpunkt</h3>
                    <p>
                        Alle Anfragen erfolgen über folgende Basis-URL:
                    </p>
                    <code>https://parkverzeichnis.de/api/v1/parks</code>

                    <h3>Authentifizierung</h3>
                    <p>
                        Für alle Anfragen benötigen Sie einen gültigen API-Key, den Sie im Parkbetreiber-Portal unter "Schnittstellen-Zugang" einsehen oder neu generieren können.
                    </p>
                    <p>
                        Der Key wird im HTTP-Header übergeben:
                    </p>
                    <code>Authorization: Bearer &lt;API_KEY&gt;</code>

                    <h3>Beispiel: Öffnungszeiten aktualisieren</h3>
                    <p>Um z. B. die Öffnungszeiten Ihres Parks zu ändern, verwenden Sie folgenden Aufruf:</p>
                    <pre><code>PATCH /api/v1/parks/123/opening-times</code></pre>
                    <p>Body (JSON):</p>
                    <pre>
                    {
                        "date": "2025-04-23",
                        "open": "09:00",
                        "close": "18:00"
                    }
                </pre>

                    <h3>Weitere Endpunkte (Auszug)</h3>
                    <ul>
                        <li><code>PATCH /parks/{id}/status</code> – z. B. bei Schließung wegen Wartung</li>
                        <li><code>PATCH /parks/{id}/waiting-times</code> – aktuelle Wartezeiten übertragen</li>
                        <li><code>PATCH /parks/{id}/info</code> – z. B. Hinweise oder Sonderaktionen</li>
                    </ul>

                    <h3>Fragen oder Hilfe benötigt?</h3>
                    <p>
                        Unser Entwicklerteam steht Ihnen bei der Integration gern zur Seite. Schreiben Sie uns an
                        <div class="my-6">
                        <button onclick="window.dispatchEvent(new CustomEvent('openContactOverlay'))" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">
                        ✉️ Kontakt aufnehmen
                        </button>
                        </div>
                         Wir freuen uns auf Ihre Nachricht!
                    </p>

                    <p><em>Mit unserer API sparen Sie Zeit, reduzieren manuelle Eingaben und machen Ihre Besucher glücklich.</em></p>
                HTML,
                'show_in_footer' => false,
                'show_in_nav' => true,
            ],

            [
                'title' => 'Über uns',
                'slug' => 'ueber-uns',
                'content' => <<<'HTML'
                    <h2>Freizeit planen. Erlebnisse finden. Erinnerungen schaffen.</h2>
                    <p>
                        Willkommen auf deiner zentralen Plattform für alles rund um <strong>Freizeitparks, Tierparks, Zoos und Wasserwelten</strong> in Deutschland und Europa! 🎢🦓💦
                    </p>

                    <p>
                        Egal ob du einen <strong>Tagesausflug mit der Familie</strong> planst, ein spannendes <strong>Abenteuer für Kinder</strong> suchst oder einfach nur neue <strong>Erlebniswelten in deiner Nähe</strong> entdecken willst – wir helfen dir dabei, den perfekten Ort zu finden.
                    </p>

                    <h3>Was du bei uns findest</h3>
                    <ul>
                        <li>🗺️ Intelligente <strong>Umkreissuche</strong> für Freizeitparks in deiner Region</li>
                        <li>🔔 Aktuelle <strong>Live-Wartezeiten</strong> bei vielen Attraktionen</li>
                        <li>☁️ <strong>Durchschnittliches Wetter</strong> zur besseren Planung</li>
                        <li>🎯 <strong>Unabhängige Bewertungen</strong> & Erfahrungsberichte echter Besucher</li>
                        <li>📸 <strong>Bilder, Videos</strong> und wissenswerte Infos zu jedem Park</li>
                    </ul>

                    <h3>Unsere Mission</h3>
                    <p>
                        Wir glauben: <strong>Freizeitplanung sollte einfach, ehrlich und inspirierend</strong> sein. Deshalb verzichten wir bewusst auf gesponserte Inhalte und stellen nur das in den Fokus, was wirklich zählt: <strong>deine Zeit, deine Erlebnisse, deine Freude</strong>.
                    </p>

                    <p>
                        Diese Plattform wurde mit viel Liebe zum Detail und einer ordentlichen Portion Freizeit-Enthusiasmus entwickelt von <a href="https://ruddattech.de" target="_blank">ruddattech.de</a>. ❤️
                    </p>

                    <h3>Bereit, neue Parks zu entdecken?</h3>
                    <p>
                        Dann starte jetzt deine Suche – finde <strong>die besten Freizeitparks, Zoos und Wasserparks</strong> für deinen nächsten Ausflug. Alle Informationen an einem Ort. Schnell. Übersichtlich. Werbefrei.
                    </p>

                    <p><em>Weil Freizeit mehr ist als nur freie Zeit – es ist deine Erlebniszeit.</em></p>
                HTML,
                'show_in_footer' => false,
                'show_in_nav' => true,
            ],
            [
                'title' => 'Presse & Kooperationen',
                'slug' => 'presse',
                'content' => <<<'HTML'
                    <h2>Presse & Kooperationen</h2>
                    <p>
                        Du bist Journalist:in, Blogger:in, Content Creator, oder arbeitest für einen Freizeitpark, Zoo oder eine Erlebniswelt? Du planst eine Medienkooperation, möchtest redaktionelle Inhalte verlinken oder dein Event bewerben? Dann bist du hier goldrichtig!
                    </p>

                    <h3>Was wir bieten</h3>
                    <ul>
                        <li>🎯 Zielgruppenrelevante Platzierungen in einem stark frequentierten Freizeitportal</li>
                        <li>📝 Redaktionelle Beiträge über neue Attraktionen, Events oder Park-Features</li>
                        <li>📸 Visuelle Präsentationen in modernem Design – auf Wunsch mit Video, Bewertungen & Umkreiskarte</li>
                        <li>🎙 Interview- oder Pressenanfragen? Sehr gerne – wir sind offen für jedes Gespräch</li>
                    </ul>

                    <h3>Was uns wichtig ist</h3>
                    <p>
                        Unsere Community liebt echte Empfehlungen, ehrliche Inhalte und nützliche Infos. Deshalb achten wir auf Transparenz, Authentizität und Mehrwert. Kooperationen müssen zu unseren Werten passen – keine plumpe Werbung, sondern echter Nutzen für unsere Nutzer:innen.
                    </p>

                    <h3>Let’s talk!</h3>
                    <p>
                        Du hast eine Idee? Willst dein Erlebnisangebot platzieren oder ein Feature anfragen? Dann schreib uns einfach:
                    </p>

                    <p>
                        <strong>Parkverzeichnis.de</strong><br>
                        Website: <a href="https://parkverzeichnis.de" target="_blank">www.parkverzeichnis.de</a>
                        <div class="my-6">
                        <button onclick="window.dispatchEvent(new CustomEvent('openContactOverlay'))" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">
                        ✉️ Kontakt aufnehmen
                        </button>
                        </div>

                    </p>

                    <p>
                        Wir freuen uns auf spannende Kooperationen, kreative Inhalte und neue Partnerschaften – vom lokalen Familienpark bis zur internationalen Freizeitmarke.
                    </p>

                    <p><em>Gemeinsam erreichen wir mehr. Und machen Freizeit für alle ein bisschen besser.</em></p>
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
