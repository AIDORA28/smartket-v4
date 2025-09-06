import { jsxs, jsx } from "react/jsx-runtime";
import { usePage, Link, Head } from "@inertiajs/react";
import { useState } from "react";
import { XMarkIcon, ChartBarIcon, CubeIcon, ShoppingCartIcon, UsersIcon, BuildingStorefrontIcon, Cog6ToothIcon, ArrowRightOnRectangleIcon, Bars3Icon, CurrencyDollarIcon } from "@heroicons/react/24/outline";
import { clsx } from "clsx";
const navigation = [
  {
    name: "Dashboard",
    href: "/dashboard",
    icon: ChartBarIcon,
    current: false
  },
  {
    name: "Productos",
    href: "/productos",
    icon: CubeIcon,
    current: false
  },
  {
    name: "Ventas",
    href: "/ventas",
    icon: ShoppingCartIcon,
    current: false
  },
  {
    name: "Clientes",
    href: "/clientes",
    icon: UsersIcon,
    current: false
  },
  {
    name: "Inventario",
    href: "/inventario",
    icon: BuildingStorefrontIcon,
    current: false
  },
  {
    name: "Configuración",
    href: "/configuracion",
    icon: Cog6ToothIcon,
    current: false
  }
];
function AuthenticatedLayout({
  children,
  header,
  title
}) {
  const { auth, flash } = usePage().props;
  const [sidebarOpen, setSidebarOpen] = useState(false);
  return /* @__PURE__ */ jsxs("div", { className: "min-h-screen bg-gray-50", children: [
    /* @__PURE__ */ jsxs("div", { className: clsx(
      "relative z-50 lg:hidden",
      sidebarOpen ? "block" : "hidden"
    ), children: [
      /* @__PURE__ */ jsx("div", { className: "fixed inset-0 bg-gray-600 bg-opacity-75" }),
      /* @__PURE__ */ jsx("div", { className: "fixed inset-0 flex", children: /* @__PURE__ */ jsxs("div", { className: "relative mr-16 flex w-full max-w-xs flex-1", children: [
        /* @__PURE__ */ jsx("div", { className: "absolute left-full top-0 flex w-16 justify-center pt-5", children: /* @__PURE__ */ jsx(
          "button",
          {
            type: "button",
            className: "-m-2.5 p-2.5",
            onClick: () => setSidebarOpen(false),
            children: /* @__PURE__ */ jsx(XMarkIcon, { className: "h-6 w-6 text-white" })
          }
        ) }),
        /* @__PURE__ */ jsxs("div", { className: "flex grow flex-col gap-y-5 overflow-y-auto bg-indigo-600 px-6 pb-4 ring-1 ring-white/10", children: [
          /* @__PURE__ */ jsx("div", { className: "flex h-16 shrink-0 items-center", children: /* @__PURE__ */ jsx("h1", { className: "text-xl font-bold text-white", children: "SmartKet v4" }) }),
          /* @__PURE__ */ jsx("nav", { className: "flex flex-1 flex-col", children: /* @__PURE__ */ jsx("ul", { role: "list", className: "flex flex-1 flex-col gap-y-7", children: /* @__PURE__ */ jsx("li", { children: /* @__PURE__ */ jsx("ul", { role: "list", className: "-mx-2 space-y-1", children: navigation.map((item) => /* @__PURE__ */ jsx("li", { children: /* @__PURE__ */ jsxs(
            Link,
            {
              href: item.href,
              className: clsx(
                item.current ? "bg-indigo-700 text-white" : "text-indigo-200 hover:text-white hover:bg-indigo-700",
                "group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
              ),
              children: [
                /* @__PURE__ */ jsx(item.icon, { className: "h-6 w-6 shrink-0" }),
                item.name
              ]
            }
          ) }, item.name)) }) }) }) })
        ] })
      ] }) })
    ] }),
    /* @__PURE__ */ jsx("div", { className: "hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col", children: /* @__PURE__ */ jsxs("div", { className: "flex grow flex-col gap-y-5 overflow-y-auto bg-indigo-600 px-6 pb-4", children: [
      /* @__PURE__ */ jsxs("div", { className: "flex h-16 shrink-0 items-center", children: [
        /* @__PURE__ */ jsx("h1", { className: "text-xl font-bold text-white", children: "SmartKet v4" }),
        /* @__PURE__ */ jsx("span", { className: "ml-2 text-xs text-indigo-200", children: auth.tenant.name })
      ] }),
      /* @__PURE__ */ jsx("nav", { className: "flex flex-1 flex-col", children: /* @__PURE__ */ jsxs("ul", { role: "list", className: "flex flex-1 flex-col gap-y-7", children: [
        /* @__PURE__ */ jsx("li", { children: /* @__PURE__ */ jsx("ul", { role: "list", className: "-mx-2 space-y-1", children: navigation.map((item) => /* @__PURE__ */ jsx("li", { children: /* @__PURE__ */ jsxs(
          Link,
          {
            href: item.href,
            className: clsx(
              item.current ? "bg-indigo-700 text-white" : "text-indigo-200 hover:text-white hover:bg-indigo-700",
              "group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200"
            ),
            children: [
              /* @__PURE__ */ jsx(item.icon, { className: "h-6 w-6 shrink-0" }),
              item.name
            ]
          }
        ) }, item.name)) }) }),
        /* @__PURE__ */ jsx("li", { className: "mt-auto", children: /* @__PURE__ */ jsxs(
          Link,
          {
            href: "/logout",
            method: "post",
            className: "group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-indigo-200 hover:bg-indigo-700 hover:text-white",
            children: [
              /* @__PURE__ */ jsx(ArrowRightOnRectangleIcon, { className: "h-6 w-6 shrink-0" }),
              "Cerrar Sesión"
            ]
          }
        ) })
      ] }) })
    ] }) }),
    /* @__PURE__ */ jsxs("div", { className: "lg:pl-72", children: [
      /* @__PURE__ */ jsxs("div", { className: "sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8", children: [
        /* @__PURE__ */ jsx(
          "button",
          {
            type: "button",
            className: "-m-2.5 p-2.5 text-gray-700 lg:hidden",
            onClick: () => setSidebarOpen(true),
            children: /* @__PURE__ */ jsx(Bars3Icon, { className: "h-6 w-6" })
          }
        ),
        /* @__PURE__ */ jsx("div", { className: "h-6 w-px bg-gray-200 lg:hidden" }),
        /* @__PURE__ */ jsxs("div", { className: "flex flex-1 gap-x-4 self-stretch lg:gap-x-6", children: [
          /* @__PURE__ */ jsx("div", { className: "relative flex flex-1 items-center", children: title && /* @__PURE__ */ jsx("h1", { className: "text-2xl font-semibold leading-6 text-gray-900", children: title }) }),
          /* @__PURE__ */ jsxs("div", { className: "flex items-center gap-x-4 lg:gap-x-6", children: [
            /* @__PURE__ */ jsx("div", { className: "hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" }),
            /* @__PURE__ */ jsxs("div", { className: "relative", children: [
              /* @__PURE__ */ jsx("span", { className: "text-sm font-medium text-gray-700", children: auth.user.name }),
              /* @__PURE__ */ jsx("p", { className: "text-xs text-gray-500", children: auth.user.email })
            ] })
          ] })
        ] })
      ] }),
      /* @__PURE__ */ jsx("main", { className: "py-10", children: /* @__PURE__ */ jsxs("div", { className: "px-4 sm:px-6 lg:px-8", children: [
        flash && /* @__PURE__ */ jsx("div", { className: clsx(
          "mb-6 rounded-md p-4",
          flash.type === "success" && "bg-green-50 border border-green-200 text-green-800",
          flash.type === "error" && "bg-red-50 border border-red-200 text-red-800",
          flash.type === "warning" && "bg-yellow-50 border border-yellow-200 text-yellow-800",
          flash.type === "info" && "bg-blue-50 border border-blue-200 text-blue-800"
        ), children: flash.message }),
        header && /* @__PURE__ */ jsx("header", { className: "mb-8", children: header }),
        children
      ] }) })
    ] })
  ] });
}
const StatCard = ({
  name,
  stat,
  icon: Icon,
  change,
  changeType
}) => /* @__PURE__ */ jsxs("div", { className: "relative overflow-hidden rounded-lg bg-white px-4 pt-5 pb-12 shadow sm:px-6 sm:pt-6", children: [
  /* @__PURE__ */ jsxs("dt", { children: [
    /* @__PURE__ */ jsx("div", { className: "absolute rounded-md bg-indigo-500 p-3", children: /* @__PURE__ */ jsx(Icon, { className: "h-6 w-6 text-white", "aria-hidden": "true" }) }),
    /* @__PURE__ */ jsx("p", { className: "ml-16 truncate text-sm font-medium text-gray-500", children: name })
  ] }),
  /* @__PURE__ */ jsxs("dd", { className: "ml-16 flex items-baseline pb-6 sm:pb-7", children: [
    /* @__PURE__ */ jsx("p", { className: "text-2xl font-semibold text-gray-900", children: stat }),
    change && /* @__PURE__ */ jsx("p", { className: `ml-2 flex items-baseline text-sm font-semibold ${changeType === "increase" ? "text-green-600" : "text-red-600"}`, children: change })
  ] })
] });
function Dashboard({ stats }) {
  return /* @__PURE__ */ jsxs(AuthenticatedLayout, { title: "Dashboard", children: [
    /* @__PURE__ */ jsx(Head, { title: "Dashboard" }),
    /* @__PURE__ */ jsxs("div", { className: "space-y-8", children: [
      /* @__PURE__ */ jsxs("div", { children: [
        /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium leading-6 text-gray-900 mb-6", children: "Resumen General" }),
        /* @__PURE__ */ jsxs("dl", { className: "grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4", children: [
          /* @__PURE__ */ jsx(
            StatCard,
            {
              name: "Ventas Totales",
              stat: stats.totalSales.toString(),
              icon: ShoppingCartIcon,
              change: "+4.75%",
              changeType: "increase"
            }
          ),
          /* @__PURE__ */ jsx(
            StatCard,
            {
              name: "Productos",
              stat: stats.totalProducts.toString(),
              icon: ChartBarIcon,
              change: "+2.02%",
              changeType: "increase"
            }
          ),
          /* @__PURE__ */ jsx(
            StatCard,
            {
              name: "Clientes",
              stat: stats.totalCustomers.toString(),
              icon: UsersIcon,
              change: "+1.39%",
              changeType: "increase"
            }
          ),
          /* @__PURE__ */ jsx(
            StatCard,
            {
              name: "Ingresos",
              stat: `S/ ${stats.totalRevenue.toLocaleString("es-PE", { minimumFractionDigits: 2 })}`,
              icon: CurrencyDollarIcon,
              change: "+3.14%",
              changeType: "increase"
            }
          )
        ] })
      ] }),
      /* @__PURE__ */ jsx("div", { className: "rounded-lg bg-white shadow", children: /* @__PURE__ */ jsxs("div", { className: "px-4 py-5 sm:p-6", children: [
        /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium leading-6 text-gray-900 mb-4", children: "Acciones Rápidas" }),
        /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4", children: [
          /* @__PURE__ */ jsx("button", { className: "relative rounded-lg border border-gray-300 bg-white px-6 py-4 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow", children: /* @__PURE__ */ jsxs("span", { className: "flex items-center space-x-3", children: [
            /* @__PURE__ */ jsx(ChartBarIcon, { className: "h-6 w-6 text-indigo-600" }),
            /* @__PURE__ */ jsx("span", { className: "text-sm font-medium text-gray-900", children: "Nueva Venta" })
          ] }) }),
          /* @__PURE__ */ jsx("button", { className: "relative rounded-lg border border-gray-300 bg-white px-6 py-4 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow", children: /* @__PURE__ */ jsxs("span", { className: "flex items-center space-x-3", children: [
            /* @__PURE__ */ jsx(CurrencyDollarIcon, { className: "h-6 w-6 text-indigo-600" }),
            /* @__PURE__ */ jsx("span", { className: "text-sm font-medium text-gray-900", children: "Agregar Producto" })
          ] }) }),
          /* @__PURE__ */ jsx("button", { className: "relative rounded-lg border border-gray-300 bg-white px-6 py-4 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow", children: /* @__PURE__ */ jsxs("span", { className: "flex items-center space-x-3", children: [
            /* @__PURE__ */ jsx(UsersIcon, { className: "h-6 w-6 text-indigo-600" }),
            /* @__PURE__ */ jsx("span", { className: "text-sm font-medium text-gray-900", children: "Nuevo Cliente" })
          ] }) }),
          /* @__PURE__ */ jsx("button", { className: "relative rounded-lg border border-gray-300 bg-white px-6 py-4 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow", children: /* @__PURE__ */ jsxs("span", { className: "flex items-center space-x-3", children: [
            /* @__PURE__ */ jsx(ChartBarIcon, { className: "h-6 w-6 text-indigo-600" }),
            /* @__PURE__ */ jsx("span", { className: "text-sm font-medium text-gray-900", children: "Ver Reportes" })
          ] }) })
        ] })
      ] }) }),
      /* @__PURE__ */ jsx("div", { className: "rounded-lg bg-white shadow", children: /* @__PURE__ */ jsxs("div", { className: "px-4 py-5 sm:p-6", children: [
        /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium leading-6 text-gray-900 mb-4", children: "Actividad Reciente" }),
        /* @__PURE__ */ jsx("div", { className: "flow-root", children: /* @__PURE__ */ jsxs("ul", { className: "-my-5 divide-y divide-gray-200", children: [
          /* @__PURE__ */ jsx("li", { className: "py-4", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-4", children: [
            /* @__PURE__ */ jsx("div", { className: "flex-shrink-0", children: /* @__PURE__ */ jsx("div", { className: "h-8 w-8 rounded-full bg-green-500 flex items-center justify-center", children: /* @__PURE__ */ jsx(ShoppingCartIcon, { className: "h-4 w-4 text-white" }) }) }),
            /* @__PURE__ */ jsxs("div", { className: "flex-1 min-w-0", children: [
              /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-900 truncate", children: "Nueva venta registrada" }),
              /* @__PURE__ */ jsx("p", { className: "text-sm text-gray-500 truncate", children: "S/ 125.50 - Cliente: María García" })
            ] }),
            /* @__PURE__ */ jsx("div", { className: "flex-shrink-0 text-sm text-gray-500", children: "Hace 2 min" })
          ] }) }),
          /* @__PURE__ */ jsx("li", { className: "py-4", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-4", children: [
            /* @__PURE__ */ jsx("div", { className: "flex-shrink-0", children: /* @__PURE__ */ jsx("div", { className: "h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center", children: /* @__PURE__ */ jsx(ChartBarIcon, { className: "h-4 w-4 text-white" }) }) }),
            /* @__PURE__ */ jsxs("div", { className: "flex-1 min-w-0", children: [
              /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-900 truncate", children: "Producto agregado" }),
              /* @__PURE__ */ jsx("p", { className: "text-sm text-gray-500 truncate", children: "Producto: Pan Integral - Stock: 50" })
            ] }),
            /* @__PURE__ */ jsx("div", { className: "flex-shrink-0 text-sm text-gray-500", children: "Hace 15 min" })
          ] }) }),
          /* @__PURE__ */ jsx("li", { className: "py-4", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-4", children: [
            /* @__PURE__ */ jsx("div", { className: "flex-shrink-0", children: /* @__PURE__ */ jsx("div", { className: "h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center", children: /* @__PURE__ */ jsx(UsersIcon, { className: "h-4 w-4 text-white" }) }) }),
            /* @__PURE__ */ jsxs("div", { className: "flex-1 min-w-0", children: [
              /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-900 truncate", children: "Cliente registrado" }),
              /* @__PURE__ */ jsx("p", { className: "text-sm text-gray-500 truncate", children: "Juan Pérez - juan@email.com" })
            ] }),
            /* @__PURE__ */ jsx("div", { className: "flex-shrink-0 text-sm text-gray-500", children: "Hace 1 hora" })
          ] }) })
        ] }) })
      ] }) })
    ] })
  ] });
}
export {
  Dashboard as default
};
