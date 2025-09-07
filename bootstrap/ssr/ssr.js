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
const appName = "SmartKet_v4_Local";
function render(page) {
  return createInertiaApp({
    page,
    render: ReactDOMServer.renderToString,
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(
      `./Pages/${name}.tsx`,
      /* @__PURE__ */ Object.assign({ "./Pages/Auth/Login.tsx": () => import("./assets/Login-G2480SHj.js"), "./Pages/Auth/Register.tsx": () => import("./assets/Register-Bg8_POC9.js"), "./Pages/Clients.tsx": () => import("./assets/Clients--t5WTafx.js"), "./Pages/Dashboard.tsx": () => import("./assets/Dashboard-CO8CO0US.js"), "./Pages/Inventory.tsx": () => import("./assets/Inventory-Dalvrv4y.js"), "./Pages/Inventory/Movements.tsx": () => import("./assets/Movements-BSu0Xw2P.js"), "./Pages/POS.tsx": () => import("./assets/POS-Bj4Fq30W.js"), "./Pages/Placeholder.tsx": () => import("./assets/Placeholder-GyOBfcWG.js"), "./Pages/Products.tsx": () => import("./assets/Products-gcz6XsIY.js"), "./Pages/Public/Landing.tsx": () => import("./assets/Landing-BJFAWIDS.js"), "./Pages/Reports.tsx": () => import("./assets/Reports-CIU24TQT.js"), "./Pages/Sales.tsx": () => import("./assets/Sales-DcjMZFoK.js") })
    ),
    setup: ({ App, props }) => /* @__PURE__ */ jsx(StrictMode, { children: /* @__PURE__ */ jsx(App, { ...props }) })
  });
}
export {
  render as default
};
