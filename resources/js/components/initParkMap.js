import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

import defaultMarkerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

// Standard-Icon-Pfade überschreiben (optional, für Fallback)
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconUrl: defaultMarkerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
});

/**
 * Initialisiert Leaflet-Karte für Parkdetailseite
 */
export function initParkMap({ lat, lng, logo, name, location, country }) {
    if (!lat || !lng) {
        const container = document.getElementById('parkMap');
        if (container) {
            container.innerHTML = '<p class="text-white p-4">Standortdaten nicht verfügbar.</p>';
        }
        return;
    }

    const map = L.map('parkMap').setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> Mitwirkende'
    }).addTo(map);

    const customIcon = L.icon({
        iconUrl: logo || defaultMarkerIcon,
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -36],
        className: 'rounded-full border-2 border-white shadow-md bg-white object-cover'
    });

    L.marker([lat, lng], { icon: customIcon }).addTo(map)
        .bindPopup(`<strong>${name}</strong><br>${location}, ${country}`)
        .openPopup();
}
