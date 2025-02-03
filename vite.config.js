import ckeditor5 from "@ckeditor/vite-plugin-ckeditor5";
import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import {defineConfig} from "vite";

export default defineConfig({
  plugins: [
    ckeditor5(),
    laravel({
      input: "resources/js/app.js",
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
