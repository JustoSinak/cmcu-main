// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; // Ajoutez cette ligne

export default defineConfig({
    root: '.',
    publicDir: 'public',
    build: {
        outDir: 'public/build', // This is the default for Laravel
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            // Suppress the public directory warning
            output: {
                manualChunks: undefined,
            }
        }
    },
    plugins: [
        laravel({
            input: [
                'resources/assets/sass/app.scss',
                'resources/assets/js/app.js',
                'resources/css/all.scss',
                'resources/js/all.js',
                'resources/js/typehead.js',
            ],
            refresh: true,
        }),
        vue({ 
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        
    ],
     resolve: {
        alias: {
            '@': '/resources/assets/js',
            '~': '/node_modules',
        },
    },
});


