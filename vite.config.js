import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny, local } from 'laravel-vite-plugin/fonts';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
            fonts: [
                // Display type. Self-hosted variable file (OFL, see
                // resources/fonts/archivo/OFL.txt) because the remote
                // providers drop the width axis the expanded headings need.
                local('Archivo', {
                    variants: [{ src: 'resources/fonts/archivo/archivo-latin-wdth-normal.woff2', weight: '100 900' }],
                    optimizedFallbacks: false,
                }),
                // Everything else on the public site: body, labels, data.
                bunny('JetBrains Mono', {
                    weights: [400, 500, 700],
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
