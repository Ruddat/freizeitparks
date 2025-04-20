import './bootstrap';
import L from 'leaflet';
import '../css/leaflet-custom.css'; // Verwende die angepasste CSS
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';


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
