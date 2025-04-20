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
