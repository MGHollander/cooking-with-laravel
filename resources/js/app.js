import "./bootstrap";
import "../scss/app.scss";

import {createInertiaApp} from "@inertiajs/vue3";
import {resolvePageComponent} from "laravel-vite-plugin/inertia-helpers";
import {createApp, h} from "vue";
import {ZiggyVue} from "ziggy-js";
import {i18nVue} from "laravel-vue-i18n";

const appName = window.document.getElementsByTagName("title")[0]?.innerText || "Koken met Marc";

createInertiaApp({
  title: (title) => {
    if (title) {
      return `${title} - ${appName}`;
    }
    return `${appName}`;
  },
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue")),
  setup({el, App, props, plugin}) {
    return createApp({render: () => h(App, props)})
      .use(plugin)
      .use(ZiggyVue)
      .use(i18nVue, {
        resolve: lang => {
          const langs = import.meta.glob('../../lang/*.json', { eager: true });
          return langs[`../../lang/${lang}.json`].default;
        },
      })
      .mount(el);
  },
  progress: {
    color: "#4B5563",
  },
});
