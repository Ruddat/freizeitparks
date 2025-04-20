import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/backend/css/app.css',
                'resources/backend/js/app.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            external: [
                '/images/marker-icon.png',
                '/images/marker-icon-2x.png',
                '/images/marker-shadow.png',
            ],
        },
        assetsInclude: [
            '**/images/marker-icon.png',
            '**/images/marker-icon-2x.png',
            '**/images/marker-shadow.png',
        ],
    },
});
