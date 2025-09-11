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
const appName = "SmartKet";
function render(page) {
  return createInertiaApp({
    page,
    render: ReactDOMServer.renderToString,
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(
      `./Pages/${name}.tsx`,
      /* @__PURE__ */ Object.assign({ "./Pages/Auth/Login-old.tsx": () => import("./assets/Login-old-B1noyECP.js"), "./Pages/Auth/Login.tsx": () => import("./assets/Login-DgZsYM-Y.js"), "./Pages/Auth/Register-old.tsx": () => import("./assets/Register-old-Cbb0rRqU.js"), "./Pages/Auth/Register.tsx": () => import("./assets/Register-Cbb0rRqU.js"), "./Pages/Clients.tsx": () => import("./assets/Clients-COKv8AYL.js"), "./Pages/Clients_Simple.tsx": () => import("./assets/Clients_Simple-4U9BmW7W.js"), "./Pages/Core/BranchManagement/Index.tsx": () => import("./assets/Index-B3Z1JYRX.js"), "./Pages/Core/Branches/Index.tsx": () => import("./assets/Index-CdfySvRx.js"), "./Pages/Core/Company/Analytics.tsx": () => import("./assets/Analytics-BmTK9L5p.js"), "./Pages/Core/Company/Settings.tsx": () => import("./assets/Settings-C-RQAK1d.js"), "./Pages/Core/CompanyManagement/Analytics/Index.tsx": () => import("./assets/Index-BKsaqNXc.js"), "./Pages/Core/CompanyManagement/Settings/Index.tsx": () => import("./assets/Index-DIcRIQFf.js"), "./Pages/Core/Users/Create.tsx": () => import("./assets/Create-C5ZXwIIf.js"), "./Pages/Core/Users/Edit.tsx": () => import("./assets/Edit-CWYTuADv.js"), "./Pages/Core/Users/Index.tsx": () => import("./assets/Index-NUNfQD1p.js"), "./Pages/Dashboard.tsx": () => import("./assets/Dashboard-DJ1qjOba.js"), "./Pages/Inventory.tsx": () => import("./assets/Inventory-BWA8oFpj.js"), "./Pages/Inventory/Movements.tsx": () => import("./assets/Movements-Cuq87k8v.js"), "./Pages/POS.tsx": () => import("./assets/POS-DZqIn4oV.js"), "./Pages/Placeholder.tsx": () => import("./assets/Placeholder-Cm5F2PCv.js"), "./Pages/ProductDetail.tsx": () => import("./assets/ProductDetail-BskE02TP.js"), "./Pages/Products.tsx": () => import("./assets/Products-DVutTs-P.js"), "./Pages/Products/Brands.tsx": () => import("./assets/Brands-C0px9HNa.js"), "./Pages/Products/Categories.tsx": () => import("./assets/Categories-CBzmg_wW.js"), "./Pages/Products/Units.tsx": () => import("./assets/Units-Cf9r3CMy.js"), "./Pages/Public/Landing-final.tsx": () => import("./assets/Landing-optimized-otzwq6UO.js").then((n) => n.L), "./Pages/Public/Landing-old.tsx": () => import("./assets/Landing-old-Chzdz2tm.js"), "./Pages/Public/Landing-optimized.tsx": () => import("./assets/Landing-optimized-otzwq6UO.js").then((n) => n.a), "./Pages/Public/Landing.tsx": () => import("./assets/Landing-Chzdz2tm.js"), "./Pages/Purchases.tsx": () => import("./assets/Purchases-BhLFcVHa.js"), "./Pages/Purchases/Create.tsx": () => import("./assets/Create-DR0iTSX_.js"), "./Pages/Reports.tsx": () => import("./assets/Reports-5xBRtzLG.js"), "./Pages/Sales.tsx": () => import("./assets/Sales-DrJ_zb6Z.js") })
    ),
    setup: ({ App, props }) => /* @__PURE__ */ jsx(StrictMode, { children: /* @__PURE__ */ jsx(App, { ...props }) })
  });
}
export {
  render as default
};
