// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; 

export default defineConfig({
    root: '.',
//    publicDir: false, 
    build: {
        manifest: 'manifest.json',
        outDir: 'public/build',
        rollupOptions: {
            input: {
               app: 'resources/assets/js/app.js',
                'app-scss': 'resources/assets/sass/app.scss',
                'all-scss': 'resources/css/all.scss',
                all: 'resources/js/all.js',
                typehead: 'resources/js/typehead.js',
            },
        },
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
            buildDirectory: 'build',
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
            // '@': '/resources/assets/js',
            '~': '/node_modules',
        },
    },
});
















