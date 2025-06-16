import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['axios'],
                    ui: ['swiper', 'gsap'],
                    utils: ['vanilla-lazyload', 'intersection-observer'],
                },
            },
        },
        assetsInlineLimit: 4096,
        cssCodeSplit: true,
        sourcemap: false,
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
    },
    optimizeDeps: {
        include: ['axios', 'swiper', 'gsap', 'vanilla-lazyload', 'intersection-observer'],
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
    define: {
        'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV),
    },
});
