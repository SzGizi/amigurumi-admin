import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    optimizeDeps: {
        include: ['@ckeditor/ckeditor5-build-classic'],
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js','resources/sass/app.scss'],
            refresh: true,
        }),
        vue(),
    ],
     resolve: {
        alias: { 
            vue: 'vue/dist/vue.esm-bundler.js' ,
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
});
