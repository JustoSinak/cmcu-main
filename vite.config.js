// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';
// import vue from '@vitejs/plugin-vue';
// import { resolve } from 'path';

// export default defineConfig({
//   root: '.',
//   plugins: [
//     laravel({
//       input: [
//         'resources/assets/sass/app.scss',
//         'resources/assets/js/app.js',
//         'resources/css/all.scss',
//         'resources/js/all.js',
//         'resources/js/typehead.js',
//       ],
//       refresh: true,
//       buildDirectory: 'build',
//     }),
//     vue({
//       template: {
//         transformAssetUrls: { base: null, includeAbsolute: false },
//       },
//     }),
//   ],
//   resolve: {
//     alias: {
//       '~': '/node_modules',
//       '@': resolve(__dirname, 'resources/assets/js'),
//     },
//   },
//   build: {
//     manifest: true,
//     outDir: 'public/build',
//     cssCodeSplit: true,
//     sourcemap: false,
//     minify: 'terser',
//     rollupOptions: {
//       input: {
//         app: 'resources/assets/js/app.js',
//         'app-scss': 'resources/assets/sass/app.scss',
//         'all-scss': 'resources/css/all.scss',
//         all: 'resources/js/all.js',
//         typehead: 'resources/js/typehead.js',
//       },
//       output: {
//         manualChunks(id) {
//           if (id.includes('node_modules')) {
//             if (id.includes('vue')) return 'vendor_vue';
//             if (id.includes('bootstrap')) return 'vendor_bootstrap';
//             return 'vendor';
//           }
//         },
//       },
//     },
//   },
// });

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
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
            '@': path.resolve(__dirname, 'resources/assets/js'),
            '~': path.resolve(__dirname, 'node_modules'),
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
    },

    // Performance optimizations
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: {
                    // Vendor splitting for better caching
                    'vendor-vue': ['vue'],
                    'vendor-bootstrap': ['bootstrap', '@popperjs/core'],
                    'vendor-charts': ['chart.js'],
                    'vendor-utils': ['lodash', 'moment', 'axios'],
                    'vendor-datatables': ['datatables.net', 'datatables.net-bs5'],
                    'vendor-editors': ['@ckeditor/ckeditor5-build-classic', 'froala-editor'],
                },
                // Asset file naming for better caching
                chunkFileNames: 'js/[name]-[hash].js',
                entryFileNames: 'js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const ext = info[info.length - 1];
                    if (/\.(png|jpe?g|svg|gif|tiff|bmp|ico)$/i.test(assetInfo.name)) {
                        return `images/[name]-[hash].${ext}`;
                    }
                    if (/\.(woff2?|eot|ttf|otf)$/i.test(assetInfo.name)) {
                        return `fonts/[name]-[hash].${ext}`;
                    }
                    return `[ext]/[name]-[hash].${ext}`;
                },
            },
        },
        // Optimize chunk size
        chunkSizeWarningLimit: 1000,
        // Better minification
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
        // Source maps only in development
        sourcemap: process.env.NODE_ENV === 'development',
    },

    // Server configuration
    server: {
        host: '127.0.0.1',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
        },
        // Windows-specific optimizations
        watch: {
            usePolling: true,
            interval: 100,
        },
    },

    // Optimize dependencies
    optimizeDeps: {
        include: [
            'vue',
            'axios',
            'lodash',
            'bootstrap',
            '@popperjs/core',
            'jquery',
            'moment',
            'chart.js',
        ],
        exclude: ['vue-demi'],
    },

    // CSS optimization
    css: {
        devSourcemap: true,
        preprocessorOptions: {
            scss: {
                additionalData: `@import "resources/assets/sass/_variables.scss";`,
            },
        },
    },
});
