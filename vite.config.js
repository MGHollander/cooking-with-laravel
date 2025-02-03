import {createRequire} from "node:module";
import ckeditor5 from "@ckeditor/vite-plugin-ckeditor5";
import vue from "@vitejs/plugin-vue";
// import laravel from "laravel-vite-plugin";
import {defineConfig} from "vite";

const require = createRequire(import.meta.url);

export default defineConfig({
  plugins: [
    ckeditor5({theme: require.resolve("@ckeditor/ckeditor5-theme-lark")}),
    // laravel({
    //   input: "resources/js/app.js",
    //   refresh: true,
    // }),
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
