<div wire:ignore wire:key="map-container">
    <div id="map" class="w-full h-[500px] rounded-lg shadow"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.leafletMapInitDone) return;
        window.leafletMapInitDone = true;

        console.log('Karte wird initialisiert...');
        const map = L.map('map').setView([51.1657, 10.4515], 5);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
        }).addTo(map);

        let markerLayer = L.layerGroup().addTo(map);

        function setMarkers({ parks, bounds }) {
            console.log('Aktualisiere Marker mit Parks:', JSON.stringify(parks, null, 2));
            console.log('Bounds:', bounds);
            markerLayer.clearLayers();

            if (!Array.isArray(parks)) {
                console.error('Parks ist kein Array:', parks);
                return;
            }

            parks.forEach((park, index) => {
                if (!park || typeof park !== 'object') {
                    console.warn(`Ungültiges Park-Objekt bei Index ${index}:`, park);
                    return;
                }

                const { latitude, longitude, name, location, status, status_label } = park;

                if (isFinite(latitude) && isFinite(longitude)) {
                    console.log(`Erstelle Marker ${index + 1}: ${name || 'Unbekannt'}, Lat: ${latitude}, Lon: ${longitude}`);
                    let marker;

                    if (status === 'open') {
                        // SVG für 'open' mit Puls-Animation
                        marker = L.marker([latitude, longitude], {
                            icon: L.divIcon({
                                html: `
                                    <svg class="marker-open" width="20" height="32" viewBox="0 0 20 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10 0C4.477 0 0 4.477 0 10c0 7.5 10 22 10 22s10-14.5 10-22C20 4.477 15.523 0 10 0z" fill="#10B981"/>
                                        <circle cx="10" cy="10" r="6" fill="white"/>
                                        <path d="M12.707 8.293a1 1 0 00-1.414 0L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 000-1.414z" fill="#10B981"/>
                                    </svg>
                                `,
                                className: '', // Vermeidet Leaflet-Standard-CSS
                                iconSize: [20, 32],
                                iconAnchor: [10, 32],
                                popupAnchor: [0, -32]
                            })
                        });
                    } else {
                        // PNGs für 'closed' und 'unknown'
                        marker = L.marker([latitude, longitude], {
                            icon: L.icon({
                                iconUrl: status === 'closed' ? '/images/marker-closed.png' : '/images/marker-unknown.png',
                                iconRetinaUrl: status === 'closed' ? '/images/marker-closed-2x.png' : '/images/marker-unknown-2x.png',
                                shadowUrl: '/images/marker-shadow.png',
                                shadowRetinaUrl: '/images/marker-shadow-2x.png',
                                iconSize: [20, 32],
                                iconAnchor: [10, 32],
                                popupAnchor: [0, -32],
                                shadowSize: [32, 32]
                            })
                        });
                    }

                    marker.bindPopup(`
                        <strong>${name || 'Unbekannt'}</strong><br>
                        ${location || 'Keine Adresse'}<br>
                        <span class="${park.status_class}">${status_label}</span>
                    `);

                    markerLayer.addLayer(marker);
                } else {
                    console.warn(`Ungültige Koordinaten für Park bei Index ${index}:`, { name, latitude, longitude });
                }
            });

            console.log(`Insgesamt ${markerLayer.getLayers().length} Marker erstellt`);

            if (bounds && bounds.minLat && bounds.maxLat && bounds.minLng && bounds.maxLng) {
                const latLngBounds = L.latLngBounds(
                    [bounds.minLat, bounds.minLng],
                    [bounds.maxLat, bounds.maxLng]
                );
                map.fitBounds(latLngBounds, {
                    padding: [50, 50],
                    maxZoom: 10
                });
                console.log('Karte fokussiert auf Bounds:', bounds);
            } else if (parks.length === 1) {
                map.setView([parks[0].latitude, parks[0].longitude], 12);
                console.log('Karte auf einzelnen Park zentriert:', parks[0]);
            } else {
                map.setView([51.1657, 10.4515], 5);
                console.log('Keine Bounds, Standardansicht verwendet');
            }

            setTimeout(() => {
                console.log('Karte wird aktualisiert...');
                map.invalidateSize();
            }, 150);
        }

        const initialParks = @json($parks);
        const initialBounds = @json($bounds);
        console.log('Initiale Parks:', JSON.stringify(initialParks, null, 2));
        console.log('Initiale Bounds:', JSON.stringify(initialBounds, null, 2));
        setMarkers({ parks: initialParks, bounds: initialBounds });

        Livewire.on('karteAktualisieren', (data) => {
            console.log('Karte aktualisieren Event empfangen:', JSON.stringify(data, null, 2));
            setMarkers(data);
        });
    });
</script>
