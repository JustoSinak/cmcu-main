import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import compression from 'vite-plugin-compression';

export default defineConfig({
    // ensure asset URLs are generated with a sane base
    base: '/',

    plugins: [
        laravel({
            input: [
                'resources/assets/sass/app.scss',
                'resources/js/app.js',
                'resources/css/all.scss',
                'resources/js/all.js',
            ],
            refresh: true,
            buildDirectory: 'build',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    // remove base:null which injected "null" into generated urls
                    includeAbsolute: false,
                },
            },
        }),
        compression({
            algorithm: 'gzip',
            ext: '.gz',
            threshold: 10240, // Only compress files > 10KB
        }),
    ],
    
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/assets/js'),
            '~': path.resolve(__dirname, 'node_modules'),
            'vue': 'vue/dist/vue.esm-bundler.js',
            // Add jQuery alias for consistency
            'jquery': 'jquery/dist/jquery.min.js',
        },
    },

    build: {
        manifest: 'manifest.json',
        outDir: 'public/build',
        
        rollupOptions: {
            output: {
                // ensure fonts go to webfonts so our $fa-font-path points there
                assetFileNames: (assetInfo) => {
                    if (/\.(woff2?|eot|ttf|otf)$/i.test(assetInfo.name)) {
                        return `webfonts/[name][extname]`;
                    }
                    if (/\.(png|jpe?g|svg|gif|tiff|bmp|ico)$/i.test(assetInfo.name)) {
                        return `images/[name]-[hash][extname]`;
                    }
                    if (/\.css$/i.test(assetInfo.name)) {
                        return `css/[name]-[hash][extname]`;
                    }
                    return `assets/[name]-[hash][extname]`;
                },
                
                chunkFileNames: 'js/[name]-[hash].js',
                entryFileNames: 'js/[name]-[hash].js',
            },
        },
        
        // Increase chunk size warning limit
        chunkSizeWarningLimit: 1500,
        
        // Aggressive minification
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info'],
                passes: 2, // Multiple passes for better compression
            },
            mangle: {
                safari10: true, // Better Safari compatibility
            },
        },
        
        // Disable source maps in production
        sourcemap: false,
        
        // Enable CSS code splitting
        cssCodeSplit: true,
        
        // Report compressed size
        reportCompressedSize: true,
    },

    // Server configuration for Windows
    server: {
        host: '127.0.0.1',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
            protocol: 'ws',
        },
        // Windows-specific optimizations
        watch: {
            usePolling: true,
            interval: 100,
            // Ignore node_modules and vendor
            ignored: ['**/node_modules/**', '**/vendor/**', '**/storage/**'],
        },
    },

    // Optimize dependencies
    optimizeDeps: {
        include: [
            'vue',
            'axios',
            'lodash-es',
            'bootstrap',
            '@popperjs/core',
            'jquery',
            'moment',
            'chart.js',
            'datatables.net-bs5',
            'datatables.net-buttons-bs5',
        ],
        exclude: ['vue-demi'],
        // Force pre-bundling for better performance
        force: false,
    },

    // CSS optimization
    css: {
        devSourcemap: false,
        preprocessorOptions: {
            scss: {
                // Reduce Sass precision for smaller file sizes
                precision: 6,
            },
        },
    },

    // Experimental features for better performance
    experimental: {
        renderBuiltUrl(filename, { hostType }) {
            if (hostType === 'js') {
                return { runtime: `window.__prependAssetUrl(${JSON.stringify(filename)})` };
            }
        },
    },

    // Esbuild options for faster builds
    esbuild: {
        legalComments: 'none',
        treeShaking: true,
    },
});