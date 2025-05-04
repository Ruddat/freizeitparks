# 🌎 Feature: SEO-Text-Generator für Freizeitparks

## 🚀 Beschreibung

Dieser Pull Request implementiert die automatische Generierung von SEO-optimierten Landingpages für Freizeitparks mithilfe von DeepInfra (Mixtral 8x7B).

## ✅ Enthaltene Features

* [x] Neue Spalte `seo_text` in der Tabelle `parks`
* [x] DeepInfra-Service für Generierung via `SeoTextGenerator`
* [x] Anpassung der Themen-Landingpages zur Anzeige des generierten Texts
* [x] Nur generierte Seiten landen in der Sitemap
* [x] Artisan-Befehl `parks:generate-seo` mit `--force` Option
* [x] Blade-Komponente `<x-park-tipps-button>` zur einfachen Verlinkung

## ⚙ Setup-Anweisungen

* `.env` um `DEEPINFRA_TOKEN` ergänzen
* Migration ausführen: `php artisan migrate`
* Texte generieren: `php artisan parks:generate-seo`

## 📊 Test-Anleitung

* [ ] Themen-Landingpage mit vorhandener SEO-Ausgabe korrekt anzeigen
* [ ] Bei fehlender `seo_text` wird dieser automatisch generiert (einmalig)
* [ ] Sitemap beinhaltet nur Landingpages mit vorhandenem Text
* [ ] Button-Komponente korrekt eingebunden

## 🌐 Screenshot / Beispielseite (optional)

> `/themen/movie-park-germany-tipps`

## 🔧 Weiterer Verbesserungsbedarf

* [ ] Admin-Button zur man. Regeneration
* [ ] Fallback bei API-Ausfall
* [ ] DeepInfra-Ausgaben vor Speichern editierbar machen (optional)

---

**Bitte nach Review mergen:**

```bash
git checkout main
git merge feature/seo-text-generator
git push origin main
```

*Thanks! 🚀*
