import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/css/theme.css',
                'resources/css/style.css',
                'resources/js/app.js',
                'resources/js/theme.js',
            ],
            refresh: true,
        }),
    ],
});
