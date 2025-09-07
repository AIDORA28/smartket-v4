import { jsxs, jsx } from "react/jsx-runtime";
import { Head, Link } from "@inertiajs/react";
import { A as AuthenticatedLayout } from "./AuthenticatedLayout-BfjI-KKz.js";
import { S as StatsCard } from "./StatsCard-DHQ101IL.js";
import { C as Card, a as CardHeader, b as CardBody, B as Button } from "./Card-DoF5IT7l.js";
import "react";
import "@heroicons/react/24/outline";
import "clsx";
function Dashboard({ auth, stats, recentSales, lowStock, empresa, sucursal, features }) {
  var _a;
  const currentTime = (/* @__PURE__ */ new Date()).toLocaleString("es-ES", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit"
  });
  const ventasHoyGrowth = Math.floor(Math.random() * 20) + 5;
  const clientesGrowth = Math.floor(Math.random() * 15) + 2;
  const facturacionGrowth = Math.floor(Math.random() * 25) + 8;
  return /* @__PURE__ */ jsxs(
    AuthenticatedLayout,
    {
      header: /* @__PURE__ */ jsxs("div", { className: "flex justify-between items-center", children: [
        /* @__PURE__ */ jsxs("div", { children: [
          /* @__PURE__ */ jsx("h2", { className: "font-bold text-2xl text-gray-900 leading-tight", children: "Dashboard Ejecutivo" }),
          /* @__PURE__ */ jsxs("p", { className: "text-sm text-gray-600 mt-1", children: [
            currentTime,
            " • ",
            empresa == null ? void 0 : empresa.nombre_empresa,
            " - ",
            sucursal == null ? void 0 : sucursal.nombre
          ] })
        ] }),
        /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-3", children: [
          /* @__PURE__ */ jsxs("div", { className: "text-right", children: [
            /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-900", children: auth.user.name }),
            /* @__PURE__ */ jsx("p", { className: "text-xs text-gray-500 capitalize", children: auth.user.rol_principal })
          ] }),
          /* @__PURE__ */ jsx("div", { className: "w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center", children: /* @__PURE__ */ jsx("span", { className: "text-white font-bold text-lg", children: auth.user.name.charAt(0).toUpperCase() }) })
        ] })
      ] }),
      children: [
        /* @__PURE__ */ jsx(Head, { title: "Dashboard - SmartKet" }),
        /* @__PURE__ */ jsx("div", { className: "py-6", children: /* @__PURE__ */ jsxs("div", { className: "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8", children: [
          /* @__PURE__ */ jsx("div", { className: "bg-gradient-to-r from-blue-600 to-purple-700 rounded-xl p-6 text-white", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
            /* @__PURE__ */ jsxs("div", { children: [
              /* @__PURE__ */ jsxs("h3", { className: "text-2xl font-bold mb-2", children: [
                "¡Bienvenido de vuelta, ",
                auth.user.name.split(" ")[0],
                "!"
              ] }),
              /* @__PURE__ */ jsx("p", { className: "text-blue-100 text-lg", children: "Todo funciona correctamente. Aquí tienes el resumen de tu negocio." })
            ] }),
            /* @__PURE__ */ jsxs("div", { className: "text-right", children: [
              /* @__PURE__ */ jsx("div", { className: "inline-flex items-center bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-2", children: [
                /* @__PURE__ */ jsx("div", { className: "w-3 h-3 bg-green-400 rounded-full animate-pulse" }),
                /* @__PURE__ */ jsxs("span", { className: "font-semibold", children: [
                  "Plan ",
                  (_a = empresa == null ? void 0 : empresa.plan) == null ? void 0 : _a.nombre
                ] })
              ] }) }),
              /* @__PURE__ */ jsx("p", { className: "text-blue-200 text-sm mt-1", children: "Estado: Activo" })
            ] })
          ] }) }),
          /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6", children: [
            /* @__PURE__ */ jsx(
              StatsCard,
              {
                title: "Ventas Hoy",
                value: `$${stats.ventasHoy.toLocaleString()}`,
                icon: "money",
                trend: {
                  value: ventasHoyGrowth,
                  label: `+${ventasHoyGrowth}% vs ayer`,
                  direction: "up"
                },
                color: "green",
                className: "bg-gradient-to-br from-green-50 to-emerald-100 border-green-200"
              }
            ),
            /* @__PURE__ */ jsx(
              StatsCard,
              {
                title: "Productos Activos",
                value: stats.productosStock,
                icon: "products",
                subtitle: "En inventario",
                color: "blue",
                className: "bg-gradient-to-br from-blue-50 to-indigo-100 border-blue-200"
              }
            ),
            /* @__PURE__ */ jsx(
              StatsCard,
              {
                title: "Clientes Activos",
                value: stats.clientesActivos,
                icon: "users",
                trend: {
                  value: clientesGrowth,
                  label: `+${clientesGrowth}% este mes`,
                  direction: "up"
                },
                color: "purple",
                className: "bg-gradient-to-br from-purple-50 to-violet-100 border-purple-200"
              }
            ),
            /* @__PURE__ */ jsx(
              StatsCard,
              {
                title: "Facturación Mensual",
                value: `$${stats.facturacionMensual.toLocaleString()}`,
                icon: "chart",
                trend: {
                  value: facturacionGrowth,
                  label: `+${facturacionGrowth}% vs mes anterior`,
                  direction: "up"
                },
                color: "orange",
                className: "bg-gradient-to-br from-orange-50 to-amber-100 border-orange-200"
              }
            )
          ] }),
          /* @__PURE__ */ jsxs(Card, { className: "border-2 border-blue-200 shadow-lg", children: [
            /* @__PURE__ */ jsx(CardHeader, { className: "bg-gradient-to-r from-blue-50 to-indigo-50", children: /* @__PURE__ */ jsx("div", { className: "flex items-center justify-between", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-3", children: [
              /* @__PURE__ */ jsx("div", { className: "w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center", children: /* @__PURE__ */ jsx("span", { className: "text-white text-xl", children: "⚡" }) }),
              /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsx("h3", { className: "text-xl font-bold text-gray-900", children: "Panel de Control Administrativo" }),
                /* @__PURE__ */ jsx("p", { className: "text-sm text-gray-600", children: "Acceso rápido a funciones principales" })
              ] })
            ] }) }) }),
            /* @__PURE__ */ jsxs(CardBody, { children: [
              /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4", children: [
                features.pos && /* @__PURE__ */ jsx(Link, { href: "/pos", children: /* @__PURE__ */ jsx(Button, { variant: "primary", size: "lg", className: "w-full justify-center h-20 bg-gradient-to-br from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-lg font-semibold shadow-lg transform hover:scale-105 transition-all", children: /* @__PURE__ */ jsxs("div", { className: "text-center", children: [
                  /* @__PURE__ */ jsx("div", { className: "text-2xl mb-1", children: "🛒" }),
                  /* @__PURE__ */ jsx("div", { children: "Punto de Venta" }),
                  /* @__PURE__ */ jsx("div", { className: "text-xs opacity-80", children: "POS Inteligente" })
                ] }) }) }),
                /* @__PURE__ */ jsx(Link, { href: "/products", children: /* @__PURE__ */ jsx(Button, { variant: "secondary", size: "lg", className: "w-full justify-center h-20 bg-gradient-to-br from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-lg font-semibold shadow-lg transform hover:scale-105 transition-all", children: /* @__PURE__ */ jsxs("div", { className: "text-center", children: [
                  /* @__PURE__ */ jsx("div", { className: "text-2xl mb-1", children: "📦" }),
                  /* @__PURE__ */ jsx("div", { children: "Inventario" }),
                  /* @__PURE__ */ jsx("div", { className: "text-xs opacity-80", children: "Gestión de productos" })
                ] }) }) }),
                /* @__PURE__ */ jsx(Link, { href: "/clients", children: /* @__PURE__ */ jsx(Button, { variant: "secondary", size: "lg", className: "w-full justify-center h-20 bg-gradient-to-br from-purple-500 to-violet-600 hover:from-purple-600 hover:to-violet-700 text-white text-lg font-semibold shadow-lg transform hover:scale-105 transition-all", children: /* @__PURE__ */ jsxs("div", { className: "text-center", children: [
                  /* @__PURE__ */ jsx("div", { className: "text-2xl mb-1", children: "👥" }),
                  /* @__PURE__ */ jsx("div", { children: "Clientes" }),
                  /* @__PURE__ */ jsx("div", { className: "text-xs opacity-80", children: "CRM & Contactos" })
                ] }) }) }),
                features.reportes && /* @__PURE__ */ jsx(Link, { href: "/reports", children: /* @__PURE__ */ jsx(Button, { variant: "secondary", size: "lg", className: "w-full justify-center h-20 bg-gradient-to-br from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white text-lg font-semibold shadow-lg transform hover:scale-105 transition-all", children: /* @__PURE__ */ jsxs("div", { className: "text-center", children: [
                  /* @__PURE__ */ jsx("div", { className: "text-2xl mb-1", children: "📊" }),
                  /* @__PURE__ */ jsx("div", { children: "Reportes" }),
                  /* @__PURE__ */ jsx("div", { className: "text-xs opacity-80", children: "Analytics & BI" })
                ] }) }) })
              ] }),
              /* @__PURE__ */ jsxs("div", { className: "mt-6 pt-6 border-t border-gray-200", children: [
                /* @__PURE__ */ jsx("h4", { className: "text-lg font-semibold text-gray-900 mb-4", children: "🔧 Herramientas Administrativas" }),
                /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4", children: [
                  /* @__PURE__ */ jsx(Link, { href: "/users", children: /* @__PURE__ */ jsx(Button, { variant: "ghost", className: "w-full justify-start p-4 h-auto", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-3", children: [
                    /* @__PURE__ */ jsx("span", { className: "text-2xl", children: "👤" }),
                    /* @__PURE__ */ jsxs("div", { className: "text-left", children: [
                      /* @__PURE__ */ jsx("div", { className: "font-medium", children: "Usuarios" }),
                      /* @__PURE__ */ jsx("div", { className: "text-sm text-gray-500", children: "Gestión de accesos" })
                    ] })
                  ] }) }) }),
                  /* @__PURE__ */ jsx(Link, { href: "/settings", children: /* @__PURE__ */ jsx(Button, { variant: "ghost", className: "w-full justify-start p-4 h-auto", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-3", children: [
                    /* @__PURE__ */ jsx("span", { className: "text-2xl", children: "⚙️" }),
                    /* @__PURE__ */ jsxs("div", { className: "text-left", children: [
                      /* @__PURE__ */ jsx("div", { className: "font-medium", children: "Configuración" }),
                      /* @__PURE__ */ jsx("div", { className: "text-sm text-gray-500", children: "Parámetros del sistema" })
                    ] })
                  ] }) }) }),
                  /* @__PURE__ */ jsx(Link, { href: "/backup", children: /* @__PURE__ */ jsx(Button, { variant: "ghost", className: "w-full justify-start p-4 h-auto", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-3", children: [
                    /* @__PURE__ */ jsx("span", { className: "text-2xl", children: "💾" }),
                    /* @__PURE__ */ jsxs("div", { className: "text-left", children: [
                      /* @__PURE__ */ jsx("div", { className: "font-medium", children: "Respaldos" }),
                      /* @__PURE__ */ jsx("div", { className: "text-sm text-gray-500", children: "Backup y restaurar" })
                    ] })
                  ] }) }) })
                ] })
              ] })
            ] })
          ] }),
          /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 lg:grid-cols-2 gap-6", children: [
            /* @__PURE__ */ jsxs(Card, { className: "shadow-lg border-gray-300", children: [
              /* @__PURE__ */ jsx(CardHeader, { className: "bg-gradient-to-r from-gray-50 to-slate-50", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
                /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-3", children: [
                  /* @__PURE__ */ jsx("div", { className: "w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center", children: /* @__PURE__ */ jsx("span", { className: "text-white text-lg", children: "💳" }) }),
                  /* @__PURE__ */ jsx("h3", { className: "text-lg font-bold text-gray-900", children: "Ventas Recientes" })
                ] }),
                /* @__PURE__ */ jsx(Link, { href: "/sales", children: /* @__PURE__ */ jsx(Button, { variant: "ghost", size: "sm", className: "text-blue-600 hover:text-blue-800", children: "Ver todas →" }) })
              ] }) }),
              /* @__PURE__ */ jsx(CardBody, { children: /* @__PURE__ */ jsxs("div", { className: "space-y-3", children: [
                recentSales.map((sale, index) => /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-100 hover:shadow-md transition-shadow", children: [
                  /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-3", children: [
                    /* @__PURE__ */ jsx("div", { className: "w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold", children: index + 1 }),
                    /* @__PURE__ */ jsxs("div", { children: [
                      /* @__PURE__ */ jsx("p", { className: "font-semibold text-gray-900", children: sale.cliente }),
                      /* @__PURE__ */ jsx("p", { className: "text-sm text-gray-600", children: /* @__PURE__ */ jsxs("span", { className: "inline-flex items-center", children: [
                        "📦 ",
                        sale.productos,
                        " productos • 🕒 ",
                        sale.fecha
                      ] }) })
                    ] })
                  ] }),
                  /* @__PURE__ */ jsxs("div", { className: "text-right", children: [
                    /* @__PURE__ */ jsxs("p", { className: "font-bold text-green-700 text-lg", children: [
                      "$",
                      sale.total.toLocaleString()
                    ] }),
                    /* @__PURE__ */ jsx("p", { className: "text-xs text-gray-500", children: "Total" })
                  ] })
                ] }, sale.id)),
                recentSales.length === 0 && /* @__PURE__ */ jsxs("div", { className: "text-center py-12", children: [
                  /* @__PURE__ */ jsx("div", { className: "w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4", children: /* @__PURE__ */ jsx("span", { className: "text-gray-400 text-2xl", children: "💳" }) }),
                  /* @__PURE__ */ jsx("p", { className: "text-gray-500 font-medium", children: "No hay ventas recientes" }),
                  /* @__PURE__ */ jsx("p", { className: "text-gray-400 text-sm mt-1", children: "Las ventas aparecerán aquí cuando realices transacciones" })
                ] })
              ] }) })
            ] }),
            /* @__PURE__ */ jsxs(Card, { className: "shadow-lg border-gray-300", children: [
              /* @__PURE__ */ jsx(CardHeader, { className: "bg-gradient-to-r from-red-50 to-orange-50", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
                /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-3", children: [
                  /* @__PURE__ */ jsx("div", { className: "w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center", children: /* @__PURE__ */ jsx("span", { className: "text-white text-lg", children: "⚠️" }) }),
                  /* @__PURE__ */ jsx("h3", { className: "text-lg font-bold text-gray-900", children: "Alertas de Stock" })
                ] }),
                /* @__PURE__ */ jsx(Link, { href: "/products", children: /* @__PURE__ */ jsx(Button, { variant: "ghost", size: "sm", className: "text-blue-600 hover:text-blue-800", children: "Ver inventario →" }) })
              ] }) }),
              /* @__PURE__ */ jsx(CardBody, { children: /* @__PURE__ */ jsxs("div", { className: "space-y-3", children: [
                lowStock.map((product, index) => /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between p-4 bg-gradient-to-r from-red-50 to-orange-50 rounded-xl border border-red-200 hover:shadow-md transition-shadow", children: [
                  /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-3", children: [
                    /* @__PURE__ */ jsx("div", { className: "w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white font-bold", children: "!" }),
                    /* @__PURE__ */ jsxs("div", { children: [
                      /* @__PURE__ */ jsx("p", { className: "font-semibold text-gray-900", children: product.nombre }),
                      /* @__PURE__ */ jsxs("p", { className: "text-sm text-gray-600", children: [
                        "Mínimo requerido: ",
                        product.minimo,
                        " unidades"
                      ] })
                    ] })
                  ] }),
                  /* @__PURE__ */ jsxs("div", { className: "text-right", children: [
                    /* @__PURE__ */ jsxs("p", { className: `font-bold text-lg ${product.stock === 0 ? "text-red-700" : "text-orange-600"}`, children: [
                      product.stock,
                      " restantes"
                    ] }),
                    /* @__PURE__ */ jsx("p", { className: `text-xs font-medium ${product.stock === 0 ? "text-red-500" : "text-orange-500"}`, children: product.stock === 0 ? "Sin stock" : "Stock bajo" })
                  ] })
                ] }, product.id)),
                lowStock.length === 0 && /* @__PURE__ */ jsxs("div", { className: "text-center py-12", children: [
                  /* @__PURE__ */ jsx("div", { className: "w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4", children: /* @__PURE__ */ jsx("span", { className: "text-green-600 text-2xl", children: "✅" }) }),
                  /* @__PURE__ */ jsx("p", { className: "text-green-700 font-semibold", children: "¡Excelente!" }),
                  /* @__PURE__ */ jsx("p", { className: "text-gray-600 text-sm mt-1", children: "Todos los productos tienen stock suficiente" })
                ] })
              ] }) })
            ] })
          ] }),
          /* @__PURE__ */ jsxs(Card, { className: "border-2 border-indigo-200 shadow-xl", children: [
            /* @__PURE__ */ jsx(CardHeader, { className: "bg-gradient-to-r from-indigo-50 to-blue-50", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
              /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-3", children: [
                /* @__PURE__ */ jsx("div", { className: "w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center", children: /* @__PURE__ */ jsx("span", { className: "text-white text-xl", children: "🖥️" }) }),
                /* @__PURE__ */ jsxs("div", { children: [
                  /* @__PURE__ */ jsx("h3", { className: "text-xl font-bold text-gray-900", children: "Centro de Monitoreo" }),
                  /* @__PURE__ */ jsx("p", { className: "text-sm text-gray-600", children: "Estado del sistema y recursos" })
                ] })
              ] }),
              /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-2", children: [
                /* @__PURE__ */ jsx("div", { className: "w-3 h-3 bg-green-400 rounded-full animate-pulse" }),
                /* @__PURE__ */ jsx("span", { className: "text-sm font-medium text-gray-700", children: "Sistema Operativo" })
              ] })
            ] }) }),
            /* @__PURE__ */ jsxs(CardBody, { children: [
              /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6", children: [
                /* @__PURE__ */ jsxs("div", { className: "text-center p-6 bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl border-2 border-green-200", children: [
                  /* @__PURE__ */ jsx("div", { className: "w-12 h-12 bg-green-500 rounded-full mx-auto mb-3 flex items-center justify-center shadow-lg", children: /* @__PURE__ */ jsx("span", { className: "text-white text-xl", children: "🗄️" }) }),
                  /* @__PURE__ */ jsx("p", { className: "font-bold text-green-900 text-lg", children: "Base de Datos" }),
                  /* @__PURE__ */ jsx("p", { className: "text-sm text-green-700 font-medium", children: "PostgreSQL" }),
                  /* @__PURE__ */ jsxs("div", { className: "mt-2 inline-flex items-center bg-green-200 px-3 py-1 rounded-full", children: [
                    /* @__PURE__ */ jsx("div", { className: "w-2 h-2 bg-green-500 rounded-full mr-2" }),
                    /* @__PURE__ */ jsx("span", { className: "text-xs font-semibold text-green-800", children: "Conectado" })
                  ] })
                ] }),
                /* @__PURE__ */ jsxs("div", { className: "text-center p-6 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl border-2 border-blue-200", children: [
                  /* @__PURE__ */ jsx("div", { className: "w-12 h-12 bg-blue-500 rounded-full mx-auto mb-3 flex items-center justify-center shadow-lg", children: /* @__PURE__ */ jsx("span", { className: "text-white text-xl", children: "💰" }) }),
                  /* @__PURE__ */ jsx("p", { className: "font-bold text-blue-900 text-lg", children: "Caja" }),
                  /* @__PURE__ */ jsx("p", { className: "text-sm text-blue-700 font-medium", children: "Sesión activa" }),
                  /* @__PURE__ */ jsxs("div", { className: "mt-2 inline-flex items-center bg-blue-200 px-3 py-1 rounded-full", children: [
                    /* @__PURE__ */ jsx("div", { className: "w-2 h-2 bg-blue-500 rounded-full mr-2" }),
                    /* @__PURE__ */ jsx("span", { className: "text-xs font-semibold text-blue-800", children: "Operativa" })
                  ] })
                ] }),
                /* @__PURE__ */ jsxs("div", { className: "text-center p-6 bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl border-2 border-purple-200", children: [
                  /* @__PURE__ */ jsx("div", { className: "w-12 h-12 bg-purple-500 rounded-full mx-auto mb-3 flex items-center justify-center shadow-lg", children: /* @__PURE__ */ jsx("span", { className: "text-white text-xl", children: "📊" }) }),
                  /* @__PURE__ */ jsx("p", { className: "font-bold text-purple-900 text-lg", children: "Analytics" }),
                  /* @__PURE__ */ jsx("p", { className: "text-sm text-purple-700 font-medium", children: "Procesando datos" }),
                  /* @__PURE__ */ jsxs("div", { className: "mt-2 inline-flex items-center bg-purple-200 px-3 py-1 rounded-full", children: [
                    /* @__PURE__ */ jsx("div", { className: "w-2 h-2 bg-purple-500 rounded-full mr-2 animate-pulse" }),
                    /* @__PURE__ */ jsx("span", { className: "text-xs font-semibold text-purple-800", children: "Activo" })
                  ] })
                ] }),
                /* @__PURE__ */ jsxs("div", { className: "text-center p-6 bg-gradient-to-br from-orange-50 to-amber-100 rounded-xl border-2 border-orange-200", children: [
                  /* @__PURE__ */ jsx("div", { className: "w-12 h-12 bg-orange-500 rounded-full mx-auto mb-3 flex items-center justify-center shadow-lg", children: /* @__PURE__ */ jsx("span", { className: "text-white text-xl", children: "💾" }) }),
                  /* @__PURE__ */ jsx("p", { className: "font-bold text-orange-900 text-lg", children: "Respaldos" }),
                  /* @__PURE__ */ jsx("p", { className: "text-sm text-orange-700 font-medium", children: "Auto backup" }),
                  /* @__PURE__ */ jsxs("div", { className: "mt-2 inline-flex items-center bg-orange-200 px-3 py-1 rounded-full", children: [
                    /* @__PURE__ */ jsx("div", { className: "w-2 h-2 bg-orange-500 rounded-full mr-2" }),
                    /* @__PURE__ */ jsx("span", { className: "text-xs font-semibold text-orange-800", children: "Programado" })
                  ] })
                ] })
              ] }),
              /* @__PURE__ */ jsx("div", { className: "mt-8 pt-6 border-t border-gray-200", children: /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 sm:grid-cols-3 gap-6", children: [
                /* @__PURE__ */ jsxs("div", { className: "text-center", children: [
                  /* @__PURE__ */ jsx("h4", { className: "text-lg font-semibold text-gray-900 mb-2", children: "🚀 Rendimiento" }),
                  /* @__PURE__ */ jsx("p", { className: "text-sm text-gray-600", children: "Sistema optimizado para alta velocidad" }),
                  /* @__PURE__ */ jsxs("div", { className: "mt-2", children: [
                    /* @__PURE__ */ jsx("div", { className: "bg-green-200 rounded-full h-2", children: /* @__PURE__ */ jsx("div", { className: "bg-green-500 h-2 rounded-full", style: { width: "94%" } }) }),
                    /* @__PURE__ */ jsx("p", { className: "text-xs text-gray-500 mt-1", children: "94% eficiencia" })
                  ] })
                ] }),
                /* @__PURE__ */ jsxs("div", { className: "text-center", children: [
                  /* @__PURE__ */ jsx("h4", { className: "text-lg font-semibold text-gray-900 mb-2", children: "🔒 Seguridad" }),
                  /* @__PURE__ */ jsx("p", { className: "text-sm text-gray-600", children: "Datos protegidos con encriptación" }),
                  /* @__PURE__ */ jsxs("div", { className: "mt-2", children: [
                    /* @__PURE__ */ jsx("div", { className: "bg-blue-200 rounded-full h-2", children: /* @__PURE__ */ jsx("div", { className: "bg-blue-500 h-2 rounded-full", style: { width: "100%" } }) }),
                    /* @__PURE__ */ jsx("p", { className: "text-xs text-gray-500 mt-1", children: "100% seguro" })
                  ] })
                ] }),
                /* @__PURE__ */ jsxs("div", { className: "text-center", children: [
                  /* @__PURE__ */ jsx("h4", { className: "text-lg font-semibold text-gray-900 mb-2", children: "📈 Disponibilidad" }),
                  /* @__PURE__ */ jsx("p", { className: "text-sm text-gray-600", children: "Sistema disponible 24/7" }),
                  /* @__PURE__ */ jsxs("div", { className: "mt-2", children: [
                    /* @__PURE__ */ jsx("div", { className: "bg-purple-200 rounded-full h-2", children: /* @__PURE__ */ jsx("div", { className: "bg-purple-500 h-2 rounded-full", style: { width: "99.8%" } }) }),
                    /* @__PURE__ */ jsx("p", { className: "text-xs text-gray-500 mt-1", children: "99.8% uptime" })
                  ] })
                ] })
              ] }) })
            ] })
          ] }),
          /* @__PURE__ */ jsx("div", { className: "text-center py-6", children: /* @__PURE__ */ jsxs("div", { className: "inline-flex items-center space-x-4 bg-white rounded-full px-6 py-3 shadow-lg border border-gray-200", children: [
            /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-2", children: [
              /* @__PURE__ */ jsx("div", { className: "w-2 h-2 bg-green-500 rounded-full animate-pulse" }),
              /* @__PURE__ */ jsx("span", { className: "text-sm font-medium text-gray-700", children: "SmartKet ERP v4.0" })
            ] }),
            /* @__PURE__ */ jsx("div", { className: "w-px h-4 bg-gray-300" }),
            /* @__PURE__ */ jsx("span", { className: "text-xs text-gray-500", children: "Última actualización: hace 2 min" }),
            /* @__PURE__ */ jsx("div", { className: "w-px h-4 bg-gray-300" }),
            /* @__PURE__ */ jsx("span", { className: "text-xs text-gray-500", children: "🔋 Sistema: Óptimo" })
          ] }) })
        ] }) })
      ]
    }
  );
}
export {
  Dashboard as default
};
