import { jsxs, jsx, Fragment } from "react/jsx-runtime";
import { useState } from "react";
import { usePage, Link, router } from "@inertiajs/react";
import { XMarkIcon, ChartBarIcon, CubeIcon, ShoppingCartIcon, UsersIcon, BuildingStorefrontIcon, DocumentChartBarIcon, Cog6ToothIcon, ArrowRightOnRectangleIcon, Bars3Icon } from "@heroicons/react/24/outline";
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
    name: "POS",
    href: "/pos",
    icon: ShoppingCartIcon,
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
    name: "Reportes",
    href: "/reportes",
    icon: DocumentChartBarIcon,
    current: false
  },
  {
    name: "Configuraciones",
    href: "/configuraciones",
    icon: Cog6ToothIcon,
    current: false
  }
];
function AuthenticatedLayout({
  children,
  header,
  title
}) {
  const { auth, empresa, sucursal, empresas_disponibles, sucursales_disponibles, flash } = usePage().props;
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const handleLogout = () => {
    router.post("/logout");
  };
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
      /* @__PURE__ */ jsxs("div", { className: "flex h-16 shrink-0 items-center justify-between", children: [
        /* @__PURE__ */ jsx("h1", { className: "text-xl font-bold text-white", children: "SmartKet v4" }),
        empresa && /* @__PURE__ */ jsx("span", { className: "text-xs text-indigo-200", children: empresa.nombre })
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
              /* @__PURE__ */ jsx("p", { className: "text-xs text-gray-500", children: auth.user.email }),
              empresa && /* @__PURE__ */ jsx("p", { className: "text-xs text-blue-600", children: empresa.nombre })
            ] }),
            /* @__PURE__ */ jsx(
              "button",
              {
                onClick: handleLogout,
                className: "text-gray-500 hover:text-gray-700",
                title: "Cerrar Sesión",
                children: /* @__PURE__ */ jsx(ArrowRightOnRectangleIcon, { className: "h-5 w-5" })
              }
            )
          ] })
        ] })
      ] }),
      /* @__PURE__ */ jsx("main", { className: "py-10", children: /* @__PURE__ */ jsxs("div", { className: "px-4 sm:px-6 lg:px-8", children: [
        flash && /* @__PURE__ */ jsxs(Fragment, { children: [
          flash.success && /* @__PURE__ */ jsx("div", { className: "mb-6 rounded-md p-4 bg-green-50 border border-green-200 text-green-800", children: flash.success }),
          flash.error && /* @__PURE__ */ jsx("div", { className: "mb-6 rounded-md p-4 bg-red-50 border border-red-200 text-red-800", children: flash.error }),
          flash.warning && /* @__PURE__ */ jsx("div", { className: "mb-6 rounded-md p-4 bg-yellow-50 border border-yellow-200 text-yellow-800", children: flash.warning }),
          flash.message && /* @__PURE__ */ jsx("div", { className: "mb-6 rounded-md p-4 bg-blue-50 border border-blue-200 text-blue-800", children: flash.message })
        ] }),
        header && /* @__PURE__ */ jsx("header", { className: "mb-8", children: header }),
        children
      ] }) })
    ] })
  ] });
}
export {
  AuthenticatedLayout as A
};
