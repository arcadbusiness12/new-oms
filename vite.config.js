import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/css/theme.css',
                'resources/css/style.css',
                'resources/css/sweetalert.css',
                'resources/css/cropper.min.css',
                'resources/js/app.js',
                'resources/js/jquery.main',
                'resources/js/theme.js',
                'resources/js/cropper.min.js',
                'resources/js/jsuites.layout.js',
            ],
            refresh: true,
        }),
    ],
});