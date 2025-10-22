import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    root: '.',
    publicDir: 'public',
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
    ],
});
