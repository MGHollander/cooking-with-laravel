import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import {defineConfig} from "vite";
import i18n from "laravel-vue-i18n/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/js/app.js",
                "resources/kocina/js/bootstrap.js",
                "resources/kocina/js/components/navbar.js",
                "resources/kocina/js/components/recipe.js",
                "resources/kocina/scss/app.scss",
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
        i18n(),
    ],
});
