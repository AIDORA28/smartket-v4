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
      /* @__PURE__ */ Object.assign({ "./Pages/Auth/Login.tsx": () => import("./assets/Login-G2480SHj.js"), "./Pages/Auth/Register.tsx": () => import("./assets/Register-Bg8_POC9.js"), "./Pages/Clients.tsx": () => import("./assets/Clients-Cfb3FeMA.js"), "./Pages/Clients_Simple.tsx": () => import("./assets/Clients_Simple-DSeV9U8e.js"), "./Pages/Dashboard-old.tsx": () => import("./assets/Dashboard-old-KPcnJFlC.js"), "./Pages/Dashboard.tsx": () => import("./assets/Dashboard-jwGwlrQ2.js"), "./Pages/Inventory.tsx": () => import("./assets/Inventory-BA0BPlPE.js"), "./Pages/Inventory/Movements.tsx": () => import("./assets/Movements-BOl5aaS0.js"), "./Pages/POS.tsx": () => import("./assets/POS-CO3Yycyl.js"), "./Pages/Placeholder.tsx": () => import("./assets/Placeholder-BaR1M6ZF.js"), "./Pages/Products.tsx": () => import("./assets/Products-l0sNRNKZ.js"), "./Pages/Products/Brands.tsx": () => import("./assets/Brands-De8BHy4W.js"), "./Pages/Products/Categories.tsx": () => import("./assets/Categories-BeAw8OU0.js"), "./Pages/Products/Units.tsx": () => import("./assets/Units-CqX7-SNf.js"), "./Pages/Public/Landing.tsx": () => import("./assets/Landing-BJFAWIDS.js"), "./Pages/Reports.tsx": () => import("./assets/Reports-CzTgF9U0.js"), "./Pages/Sales.tsx": () => import("./assets/Sales-BKOA9RuO.js") })
    ),
    setup: ({ App, props }) => /* @__PURE__ */ jsx(StrictMode, { children: /* @__PURE__ */ jsx(App, { ...props }) })
  });
}
export {
  render as default
};
