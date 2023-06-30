import "./bootstrap";
import "../scss/app.scss";

import CKEditor from "@ckeditor/ckeditor5-vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";

const appName = window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

createInertiaApp({
  title: (title) => {
    if (title) {
      return `${title} - ${appName}`;
    }
    return `${appName}`;
  },
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue")),
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      .use(CKEditor)
      .use(FontAwesomeIcon)
      .mount(el);
  },
  progress: {
    color: "#4B5563",
  },
});
