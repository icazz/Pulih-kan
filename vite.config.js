import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'], // Gunakan array untuk fleksibilitas
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
    // Tambahkan bagian ini untuk memastikan build lebih stabil di cloud
    build: {
        chunkSizeWarningLimit: 1600,
        manifest: true,
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});