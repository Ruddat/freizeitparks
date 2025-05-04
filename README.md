# 🎢 Freizeitpark

Ein modernes Laravel-Projekt zur Übersicht und Bewertung von Freizeitparks in Europa – mit Live-Daten, Wetter, Warteschlangen, Besucherfeedback, Flipcards, Kartendarstellung und vielem mehr!

---

## 🚀 Features

- 🌦 Live-Wettervorhersage pro Park
- ⏱ Live-Wartezeiten per API
- 📍 Kartenansicht mit Radiusfilter
- 🧑‍🤝‍🧑 Besucherbewertungen (Crowd-Level, Kommentar, Sauberkeit, etc.)
- 🛰️ Automatisches Tracking von Besuchern (GeoIP)
- 🎬 YouTube/Vimeo/MP4-Videos direkt im Parkprofil
- 💡 Flip-Cards mit animierten Effekten & Bewertung
- 🧠 Automatische SEO-Textgenerierung
- 🗂 Dynamische sitemap.xml
- 💾 Backup-Manager (manuell & automatisch)

---

## ⚙️ Projekt Setup

### Voraussetzungen

- PHP >= 8.1
- Composer
- Node.js + npm
- MySQL
- Laravel 10/11

### Installation

```bash
git clone https://github.com/dein-user/freizeitpark.git
cd freizeitpark

cp .env.example .env
composer install
npm install && npm run build

php artisan key:generate
php artisan migrate
php artisan db:seed   # optional: Beispieldaten laden
php artisan serve
```

---

## 🗺️ Datenquellen

- Wetterdaten: [Open-Meteo API](https://open-meteo.com/)
- Geodaten: [OpenStreetMap / Nominatim](https://nominatim.openstreetmap.org/)
- Besucherdaten: Eigene Datenbank + Crowd-API

---

## 🛠 Technologien

- Laravel 10+
- TailwindCSS
- Alpine.js
- Leaflet.js
- Axios
- Blade Templates
- SQLite/MySQL

---

## 📸 Screenshots



---

## 🤝 Mitwirken

Pull Requests willkommen!  
Bei Fehlern, Ideen oder Vorschlägen bitte ein Issue eröffnen.

---

## 📄 Lizenz

MIT License – frei nutzbar mit Namensnennung.
