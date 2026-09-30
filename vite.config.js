import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
            fonts: [
                // One family for everything readable: 600 for headings,
                // 400/500 for body and UI.
                bunny('Schibsted Grotesk', {
                    weights: [400, 500, 600],
                }),
                // Dates, labels and stack tokens only.
                bunny('JetBrains Mono', {
                    weights: [400, 500],
                }),
            ],
        }),
        react(),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
