import {createRequire} from 'node:module';
import {defineConfig} from 'vite';
import ckeditor5 from '@ckeditor/vite-plugin-ckeditor5';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

const require = createRequire(import.meta.url);

export default defineConfig({
    plugins: [
        ckeditor5({theme: require.resolve('@ckeditor/ckeditor5-theme-lark')}),
        laravel({
            input: 'resources/js/app.js',
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
});
