// import _ from "lodash";
//
// window._ = _;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// import axios from "axios";
//
// window.axios = axios;
//
// window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });

import Alpine from "alpinejs";
import Swup from "swup";
import SwupDebugPlugin from "@swup/debug-plugin";
import SwupFragmentPlugin from "@swup/fragment-plugin";
import SwupFormsPlugin from "@swup/forms-plugin";
import SwupScriptsPlugin from "@swup/scripts-plugin";

window.Alpine = Alpine;
Alpine.start();

/**
 * Swup is a versatile and extensible page transition library for server-rendered
 * websites. It manages the complete page load lifecycle and smoothly animates
 * between the current and next page. In addition, it offers many other
 * quality-of-life improvements like caching, smart preloading, native browser
 * history and enhanced accessibility.
 */

const swup = new Swup({
  animateHistoryBrowsing: true,
  plugins: [
    new SwupDebugPlugin(),
    new SwupFormsPlugin(),
    new SwupFragmentPlugin({
      debug: true,
      rules: [
        {
          containers: ["#recipes", "#recipes-title", "#recipes-pagination"],
          from: ["/blade", "/blade\\?search=:filter?", "/blade\\?page=:filter?"],
          to: ["/blade", "/blade\\?search=:filter?", "/blade\\?page=:filter?"],
        },
      ],
    }),
    new SwupScriptsPlugin(),
  ],
});

function setTransitionDelays() {
  document.querySelectorAll(".recipe").forEach((el, i) => {
    el.style.transitionDelay = i * 30 + "ms";
  });
}

setTransitionDelays();
swup.hooks.on("page:view", setTransitionDelays);
