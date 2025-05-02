import './bootstrap';
import L from 'leaflet';
import '../css/leaflet-custom.css'; // Verwende die angepasste CSS
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';
import '@lottiefiles/lottie-player';
import { initParkMap } from './components/initParkMap';

window.L = L; // Macht Leaflet global verfügbar







// ✅ Beispiel: Swiper init
document.addEventListener('DOMContentLoaded', function () {
    if (document.querySelector('.nearbySwiper')) {
        new Swiper('.nearbySwiper', {
            loop: true,
            slidesPerView: 1,
            spaceBetween: 10,
            centeredSlides: true,
            grabCursor: true,
            lazy: {
                loadPrevNext: true,
                loadPrevNextAmount: 1,
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: true,
                pauseOnMouseEnter: true,
            },
            breakpoints: {
                320: { slidesPerView: 2, spaceBetween: 10 },
                640: { slidesPerView: 2, spaceBetween: 20 },
                820: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 30 },
                1280: { slidesPerView: 5, spaceBetween: 40 },
            },
        });
    }
});


// Verweildauer-Tracking
let startTime = Date.now();

function sendDwellTime() {
    const dwellTime = Math.floor((Date.now() - startTime) / 1000);
    const sessionId = document.querySelector('meta[name="session-id"]').content;
    const pageUrl = window.location.href;

    fetch('/track-dwell-time', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            session_id: sessionId,
            dwell_time: dwellTime,
            page_url: pageUrl,
        }),
    }).catch(error => console.error('Fehler beim Senden der Verweildauer:', error));
}

window.addEventListener('beforeunload', sendDwellTime);

setInterval(() => {
    sendDwellTime();
    startTime = Date.now();
}, 10000);


document.addEventListener('DOMContentLoaded', async () => {
    if (document.querySelector('lottie-player')) {
        try {
            await import('@lottiefiles/lottie-player');
            console.log('LottiePlayer geladen');
        } catch (error) {
            console.error('Fehler beim Laden von LottiePlayer:', error);
        }
    }
});


document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('parkMap');
    if (el) {
        const lat = parseFloat(el.dataset.lat);
        const lng = parseFloat(el.dataset.lng);
        const logo = el.dataset.logo;
        const name = el.dataset.name;
        const location = el.dataset.location;
        const country = el.dataset.country;

        initParkMap({ lat, lng, logo, name, location, country });
    }
});
