import { jsxs, jsx } from "react/jsx-runtime";
import { useState } from "react";
import { Head, Link } from "@inertiajs/react";
import { clsx } from "clsx";
import { A as AuthenticatedLayout } from "./AuthenticatedLayout-BfjI-KKz.js";
import { C as Card, a as CardHeader, b as CardBody, B as Button } from "./Card-DoF5IT7l.js";
import { AdjustmentsHorizontalIcon, MagnifyingGlassIcon, EyeIcon, PencilIcon, TrashIcon, PlusIcon, CloudArrowDownIcon } from "@heroicons/react/24/outline";
function Products({ auth, products, categories, filters }) {
  const [searchTerm, setSearchTerm] = useState(filters.search || "");
  const [selectedCategory, setSelectedCategory] = useState(filters.category || "all");
  const [selectedStatus, setSelectedStatus] = useState(filters.status || "all");
  const filteredProducts = products.filter((product) => {
    var _a;
    const matchesSearch = product.nombre.toLowerCase().includes(searchTerm.toLowerCase()) || ((_a = product.descripcion) == null ? void 0 : _a.toLowerCase().includes(searchTerm.toLowerCase()));
    const matchesCategory = selectedCategory === "all" || product.categoria === selectedCategory;
    const matchesStatus = selectedStatus === "all" || selectedStatus === "active" && product.activo || selectedStatus === "inactive" && !product.activo || selectedStatus === "low_stock" && product.stock <= product.minimo;
    return matchesSearch && matchesCategory && matchesStatus;
  });
  const stats = {
    total: products.length,
    active: products.filter((p) => p.activo).length,
    lowStock: products.filter((p) => p.stock <= p.minimo).length,
    categories: new Set(products.map((p) => p.categoria)).size
  };
  return /* @__PURE__ */ jsxs(
    AuthenticatedLayout,
    {
      header: /* @__PURE__ */ jsxs("div", { className: "flex justify-between items-center", children: [
        /* @__PURE__ */ jsx("h2", { className: "font-semibold text-xl text-gray-800 leading-tight", children: "Gestión de Productos" }),
        /* @__PURE__ */ jsxs("div", { className: "flex gap-3", children: [
          /* @__PURE__ */ jsxs(Button, { variant: "secondary", size: "sm", children: [
            /* @__PURE__ */ jsx(CloudArrowDownIcon, { className: "w-4 h-4 mr-2" }),
            "Exportar"
          ] }),
          /* @__PURE__ */ jsx(Link, { href: "/products/create", children: /* @__PURE__ */ jsxs(Button, { variant: "primary", size: "sm", children: [
            /* @__PURE__ */ jsx(PlusIcon, { className: "w-4 h-4 mr-2" }),
            "Nuevo Producto"
          ] }) })
        ] })
      ] }),
      children: [
        /* @__PURE__ */ jsx(Head, { title: "Productos" }),
        /* @__PURE__ */ jsx("div", { className: "py-6", children: /* @__PURE__ */ jsxs("div", { className: "max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6", children: [
          /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 md:grid-cols-4 gap-4", children: [
            /* @__PURE__ */ jsx("div", { className: "bg-white p-4 rounded-lg shadow border", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
              /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-600", children: "Total Productos" }),
                /* @__PURE__ */ jsx("p", { className: "text-2xl font-bold text-gray-900", children: stats.total })
              ] }),
              /* @__PURE__ */ jsx("div", { className: "w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center", children: /* @__PURE__ */ jsx("span", { className: "text-blue-600 text-xl", children: "📦" }) })
            ] }) }),
            /* @__PURE__ */ jsx("div", { className: "bg-white p-4 rounded-lg shadow border", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
              /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-600", children: "Productos Activos" }),
                /* @__PURE__ */ jsx("p", { className: "text-2xl font-bold text-green-900", children: stats.active })
              ] }),
              /* @__PURE__ */ jsx("div", { className: "w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center", children: /* @__PURE__ */ jsx("span", { className: "text-green-600 text-xl", children: "✅" }) })
            ] }) }),
            /* @__PURE__ */ jsx("div", { className: "bg-white p-4 rounded-lg shadow border", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
              /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-600", children: "Stock Bajo" }),
                /* @__PURE__ */ jsx("p", { className: "text-2xl font-bold text-red-900", children: stats.lowStock })
              ] }),
              /* @__PURE__ */ jsx("div", { className: "w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center", children: /* @__PURE__ */ jsx("span", { className: "text-red-600 text-xl", children: "⚠️" }) })
            ] }) }),
            /* @__PURE__ */ jsx("div", { className: "bg-white p-4 rounded-lg shadow border", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
              /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-600", children: "Categorías" }),
                /* @__PURE__ */ jsx("p", { className: "text-2xl font-bold text-purple-900", children: stats.categories })
              ] }),
              /* @__PURE__ */ jsx("div", { className: "w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center", children: /* @__PURE__ */ jsx("span", { className: "text-purple-600 text-xl", children: "🏷️" }) })
            ] }) })
          ] }),
          /* @__PURE__ */ jsxs(Card, { children: [
            /* @__PURE__ */ jsx(CardHeader, { children: /* @__PURE__ */ jsxs("div", { className: "flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4", children: [
              /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900", children: "Filtros y Búsqueda" }),
              /* @__PURE__ */ jsxs("div", { className: "flex items-center gap-2", children: [
                /* @__PURE__ */ jsx(AdjustmentsHorizontalIcon, { className: "w-5 h-5 text-gray-500" }),
                /* @__PURE__ */ jsxs("span", { className: "text-sm text-gray-500", children: [
                  filteredProducts.length,
                  " de ",
                  products.length,
                  " productos"
                ] })
              ] })
            ] }) }),
            /* @__PURE__ */ jsx(CardBody, { children: /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 md:grid-cols-3 gap-4", children: [
              /* @__PURE__ */ jsxs("div", { className: "relative", children: [
                /* @__PURE__ */ jsx(MagnifyingGlassIcon, { className: "absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" }),
                /* @__PURE__ */ jsx(
                  "input",
                  {
                    type: "text",
                    placeholder: "Buscar productos...",
                    className: "pl-10 pr-4 py-2 w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500",
                    value: searchTerm,
                    onChange: (e) => setSearchTerm(e.target.value)
                  }
                )
              ] }),
              /* @__PURE__ */ jsxs(
                "select",
                {
                  className: "px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500",
                  value: selectedCategory,
                  onChange: (e) => setSelectedCategory(e.target.value),
                  children: [
                    /* @__PURE__ */ jsx("option", { value: "all", children: "Todas las categorías" }),
                    categories.map((category) => /* @__PURE__ */ jsx("option", { value: category, children: category }, category))
                  ]
                }
              ),
              /* @__PURE__ */ jsxs(
                "select",
                {
                  className: "px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500",
                  value: selectedStatus,
                  onChange: (e) => setSelectedStatus(e.target.value),
                  children: [
                    /* @__PURE__ */ jsx("option", { value: "all", children: "Todos los estados" }),
                    /* @__PURE__ */ jsx("option", { value: "active", children: "Activos" }),
                    /* @__PURE__ */ jsx("option", { value: "inactive", children: "Inactivos" }),
                    /* @__PURE__ */ jsx("option", { value: "low_stock", children: "Stock bajo" })
                  ]
                }
              )
            ] }) })
          ] }),
          /* @__PURE__ */ jsxs(Card, { children: [
            /* @__PURE__ */ jsx(CardHeader, { children: /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900", children: "Lista de Productos" }) }),
            /* @__PURE__ */ jsx(CardBody, { children: /* @__PURE__ */ jsxs("div", { className: "overflow-x-auto", children: [
              /* @__PURE__ */ jsxs("table", { className: "min-w-full divide-y divide-gray-200", children: [
                /* @__PURE__ */ jsx("thead", { className: "bg-gray-50", children: /* @__PURE__ */ jsxs("tr", { children: [
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Producto" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Categoría" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Precio" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Stock" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Estado" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Acciones" })
                ] }) }),
                /* @__PURE__ */ jsx("tbody", { className: "bg-white divide-y divide-gray-200", children: filteredProducts.map((product) => /* @__PURE__ */ jsxs("tr", { className: "hover:bg-gray-50", children: [
                  /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center", children: [
                    /* @__PURE__ */ jsx("div", { className: "flex-shrink-0 h-10 w-10", children: product.imagen ? /* @__PURE__ */ jsx(
                      "img",
                      {
                        className: "h-10 w-10 rounded-lg object-cover",
                        src: product.imagen,
                        alt: product.nombre
                      }
                    ) : /* @__PURE__ */ jsx("div", { className: "h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center", children: /* @__PURE__ */ jsx("span", { className: "text-gray-400 text-lg", children: "📦" }) }) }),
                    /* @__PURE__ */ jsxs("div", { className: "ml-4", children: [
                      /* @__PURE__ */ jsx("div", { className: "text-sm font-medium text-gray-900", children: product.nombre }),
                      product.descripcion && /* @__PURE__ */ jsx("div", { className: "text-sm text-gray-500", children: product.descripcion.length > 50 ? `${product.descripcion.substring(0, 50)}...` : product.descripcion })
                    ] })
                  ] }) }),
                  /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsx("span", { className: "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800", children: product.categoria }) }),
                  /* @__PURE__ */ jsxs("td", { className: "px-6 py-4 whitespace-nowrap text-sm text-gray-900", children: [
                    "$",
                    product.precio.toLocaleString()
                  ] }),
                  /* @__PURE__ */ jsxs("td", { className: "px-6 py-4 whitespace-nowrap", children: [
                    /* @__PURE__ */ jsxs("div", { className: "text-sm text-gray-900", children: [
                      product.stock,
                      " unidades"
                    ] }),
                    /* @__PURE__ */ jsxs("div", { className: "text-sm text-gray-500", children: [
                      "Mín: ",
                      product.minimo
                    ] }),
                    product.stock <= product.minimo && /* @__PURE__ */ jsx("div", { className: "text-xs text-red-600 font-medium", children: "⚠️ Stock bajo" })
                  ] }),
                  /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsx("span", { className: clsx(
                    "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium",
                    product.activo ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800"
                  ), children: product.activo ? "Activo" : "Inactivo" }) }),
                  /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap text-right text-sm font-medium", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-end gap-2", children: [
                    /* @__PURE__ */ jsx(Link, { href: `/products/${product.id}`, children: /* @__PURE__ */ jsx(Button, { variant: "ghost", size: "sm", children: /* @__PURE__ */ jsx(EyeIcon, { className: "w-4 h-4" }) }) }),
                    /* @__PURE__ */ jsx(Link, { href: `/products/${product.id}/edit`, children: /* @__PURE__ */ jsx(Button, { variant: "ghost", size: "sm", children: /* @__PURE__ */ jsx(PencilIcon, { className: "w-4 h-4" }) }) }),
                    /* @__PURE__ */ jsx(Button, { variant: "ghost", size: "sm", className: "text-red-600 hover:text-red-700", children: /* @__PURE__ */ jsx(TrashIcon, { className: "w-4 h-4" }) })
                  ] }) })
                ] }, product.id)) })
              ] }),
              filteredProducts.length === 0 && /* @__PURE__ */ jsxs("div", { className: "text-center py-12", children: [
                /* @__PURE__ */ jsx("div", { className: "text-gray-400 text-6xl mb-4", children: "📦" }),
                /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900 mb-2", children: "No se encontraron productos" }),
                /* @__PURE__ */ jsx("p", { className: "text-gray-500 mb-6", children: searchTerm || selectedCategory !== "all" || selectedStatus !== "all" ? "Intenta ajustar los filtros de búsqueda" : "Comienza agregando tu primer producto" }),
                !searchTerm && selectedCategory === "all" && selectedStatus === "all" && /* @__PURE__ */ jsx(Link, { href: "/products/create", children: /* @__PURE__ */ jsxs(Button, { variant: "primary", children: [
                  /* @__PURE__ */ jsx(PlusIcon, { className: "w-4 h-4 mr-2" }),
                  "Crear Primer Producto"
                ] }) })
              ] })
            ] }) })
          ] })
        ] }) })
      ]
    }
  );
}
export {
  Products as default
};
