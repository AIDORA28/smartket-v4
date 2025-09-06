import { jsx } from "react/jsx-runtime";
import axios from "axios";
import { createInertiaApp } from "@inertiajs/react";
import ReactDOMServer from "react-dom/server";
import { StrictMode } from "react";
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
async function resolvePageComponent(path, pages) {
  for (const p of Array.isArray(path) ? path : [path]) {
    const page = pages[p];
    if (typeof page === "undefined") {
      continue;
    }
    return typeof page === "function" ? page() : page;
  }
  throw new Error(`Page not found: ${path}`);
}
const appName = "Laravel";
createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(
    `./Pages/${name}.tsx`,
    /* @__PURE__ */ Object.assign({ "./Pages/Dashboard.tsx": () => import("./assets/Dashboard-OTO4DwuT.js") })
  ),
  setup: ({ App, props }) => {
    return ReactDOMServer.renderToString(
      /* @__PURE__ */ jsx(StrictMode, { children: /* @__PURE__ */ jsx(App, { ...props }) })
    );
  },
  progress: {
    color: "#4F46E5",
    showSpinner: true
  }
});
