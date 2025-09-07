import { jsx, jsxs } from "react/jsx-runtime";
import { useState } from "react";
import { Head, Link, router } from "@inertiajs/react";
import { A as AuthenticatedLayout } from "./AuthenticatedLayout-BfjI-KKz.js";
import { C as Card, B as Button } from "./Button-DrUMbt6l.js";
import { ChartBarIcon, ExclamationTriangleIcon, MagnifyingGlassIcon, ClockIcon, AdjustmentsHorizontalIcon } from "@heroicons/react/24/outline";
import "clsx";
function StatsCard({ title, value, icon: Icon, trend = "neutral", className = "" }) {
  const trendColors = {
    up: "text-green-600",
    down: "text-red-600",
    neutral: "text-gray-600"
  };
  const borderColors = {
    up: "border-green-200",
    down: "border-red-200",
    neutral: "border-gray-200"
  };
  return /* @__PURE__ */ jsx("div", { className: `bg-white overflow-hidden shadow rounded-lg border-l-4 ${borderColors[trend]} ${className}`, children: /* @__PURE__ */ jsx("div", { className: "p-5", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center", children: [
    /* @__PURE__ */ jsx("div", { className: "flex-shrink-0", children: /* @__PURE__ */ jsx(Icon, { className: `h-6 w-6 ${trendColors[trend]}` }) }),
    /* @__PURE__ */ jsx("div", { className: "ml-5 w-0 flex-1", children: /* @__PURE__ */ jsxs("dl", { children: [
      /* @__PURE__ */ jsx("dt", { className: "text-sm font-medium text-gray-500 truncate", children: title }),
      /* @__PURE__ */ jsx("dd", { className: "text-lg font-medium text-gray-900", children: value })
    ] }) })
  ] }) }) });
}
const stockStatusColors = {
  normal: "bg-green-100 text-green-800",
  bajo: "bg-yellow-100 text-yellow-800",
  sin_stock: "bg-red-100 text-red-800",
  exceso: "bg-blue-100 text-blue-800"
};
const stockStatusLabels = {
  normal: "Normal",
  bajo: "Stock Bajo",
  sin_stock: "Sin Stock",
  exceso: "Exceso"
};
function Inventory({ products, stats, categories, filters, error }) {
  const [search, setSearch] = useState(filters.search || "");
  const [selectedCategory, setSelectedCategory] = useState(filters.categoria || "");
  const [stockFilter, setStockFilter] = useState(filters.stock_filter || "todos");
  const [showAdjustModal, setShowAdjustModal] = useState(false);
  const [selectedProduct, setSelectedProduct] = useState(null);
  const [adjustmentData, setAdjustmentData] = useState({
    cantidad: "",
    tipo_ajuste: "entrada",
    motivo: ""
  });
  if (error) {
    return /* @__PURE__ */ jsxs(AuthenticatedLayout, { children: [
      /* @__PURE__ */ jsx(Head, { title: "Inventario" }),
      /* @__PURE__ */ jsx("div", { className: "py-12", children: /* @__PURE__ */ jsx("div", { className: "max-w-7xl mx-auto sm:px-6 lg:px-8", children: /* @__PURE__ */ jsx("div", { className: "bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded", children: error }) }) })
    ] });
  }
  const handleSearch = () => {
    router.get("/inventario", {
      search,
      categoria: selectedCategory,
      stock_filter: stockFilter,
      sort_by: filters.sort_by,
      sort_direction: filters.sort_direction
    }, { preserveState: true });
  };
  const handleSort = (column) => {
    const direction = filters.sort_by === column && filters.sort_direction === "asc" ? "desc" : "asc";
    router.get("/inventario", {
      ...filters,
      sort_by: column,
      sort_direction: direction
    }, { preserveState: true });
  };
  const openAdjustModal = (product) => {
    setSelectedProduct(product);
    setShowAdjustModal(true);
    setAdjustmentData({
      cantidad: "",
      tipo_ajuste: "entrada",
      motivo: ""
    });
  };
  const handleAdjustStock = async () => {
    if (!selectedProduct || !adjustmentData.cantidad || !adjustmentData.motivo) {
      return;
    }
    try {
      await router.post("/inventario/adjust-stock", {
        producto_id: selectedProduct.id,
        cantidad: parseFloat(adjustmentData.cantidad),
        tipo_ajuste: adjustmentData.tipo_ajuste,
        motivo: adjustmentData.motivo
      });
      setShowAdjustModal(false);
      setSelectedProduct(null);
      router.reload();
    } catch (error2) {
      console.error("Error adjusting stock:", error2);
    }
  };
  return /* @__PURE__ */ jsxs(AuthenticatedLayout, { children: [
    /* @__PURE__ */ jsx(Head, { title: "Inventario" }),
    /* @__PURE__ */ jsx("div", { className: "py-12", children: /* @__PURE__ */ jsxs("div", { className: "max-w-7xl mx-auto sm:px-6 lg:px-8", children: [
      /* @__PURE__ */ jsxs("div", { className: "mb-8", children: [
        /* @__PURE__ */ jsx("h1", { className: "text-3xl font-bold text-gray-900", children: "Inventario" }),
        /* @__PURE__ */ jsx("p", { className: "mt-2 text-gray-600", children: "Gestión y control de stock de productos" })
      ] }),
      /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 md:grid-cols-4 gap-6 mb-8", children: [
        /* @__PURE__ */ jsx(
          StatsCard,
          {
            title: "Total Productos",
            value: stats.total_productos.toString(),
            icon: ChartBarIcon,
            trend: "neutral"
          }
        ),
        /* @__PURE__ */ jsx(
          StatsCard,
          {
            title: "Stock Bajo",
            value: stats.productos_stock_bajo.toString(),
            icon: ExclamationTriangleIcon,
            trend: stats.productos_stock_bajo > 0 ? "down" : "neutral",
            className: stats.productos_stock_bajo > 0 ? "border-yellow-200" : ""
          }
        ),
        /* @__PURE__ */ jsx(
          StatsCard,
          {
            title: "Sin Stock",
            value: stats.productos_sin_stock.toString(),
            icon: ExclamationTriangleIcon,
            trend: stats.productos_sin_stock > 0 ? "down" : "neutral",
            className: stats.productos_sin_stock > 0 ? "border-red-200" : ""
          }
        ),
        /* @__PURE__ */ jsx(
          StatsCard,
          {
            title: "Valor Inventario",
            value: `$${stats.valor_inventario.toLocaleString()}`,
            icon: ChartBarIcon,
            trend: "neutral"
          }
        )
      ] }),
      /* @__PURE__ */ jsx(Card, { className: "mb-6", children: /* @__PURE__ */ jsxs("div", { className: "flex flex-wrap gap-4 items-center", children: [
        /* @__PURE__ */ jsx("div", { className: "flex-1 min-w-64", children: /* @__PURE__ */ jsxs("div", { className: "relative", children: [
          /* @__PURE__ */ jsx(MagnifyingGlassIcon, { className: "absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" }),
          /* @__PURE__ */ jsx(
            "input",
            {
              type: "text",
              placeholder: "Buscar productos...",
              value: search,
              onChange: (e) => setSearch(e.target.value),
              onKeyPress: (e) => e.key === "Enter" && handleSearch(),
              className: "pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
            }
          )
        ] }) }),
        /* @__PURE__ */ jsxs(
          "select",
          {
            value: selectedCategory,
            onChange: (e) => setSelectedCategory(e.target.value),
            className: "border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500",
            children: [
              /* @__PURE__ */ jsx("option", { value: "", children: "Todas las categorías" }),
              categories.map((category) => /* @__PURE__ */ jsx("option", { value: category.id, children: category.nombre }, category.id))
            ]
          }
        ),
        /* @__PURE__ */ jsxs(
          "select",
          {
            value: stockFilter,
            onChange: (e) => setStockFilter(e.target.value),
            className: "border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500",
            children: [
              /* @__PURE__ */ jsx("option", { value: "todos", children: "Todos los productos" }),
              /* @__PURE__ */ jsx("option", { value: "bajo", children: "Stock bajo" }),
              /* @__PURE__ */ jsx("option", { value: "sin_stock", children: "Sin stock" }),
              /* @__PURE__ */ jsx("option", { value: "exceso", children: "Stock en exceso" })
            ]
          }
        ),
        /* @__PURE__ */ jsx(Button, { onClick: handleSearch, children: "Filtrar" }),
        /* @__PURE__ */ jsx(Link, { href: "/inventario/movements", children: /* @__PURE__ */ jsxs(Button, { variant: "outline", children: [
          /* @__PURE__ */ jsx(ClockIcon, { className: "h-4 w-4 mr-2" }),
          "Ver Movimientos"
        ] }) })
      ] }) }),
      /* @__PURE__ */ jsxs(Card, { children: [
        /* @__PURE__ */ jsx("div", { className: "overflow-x-auto", children: /* @__PURE__ */ jsxs("table", { className: "min-w-full divide-y divide-gray-200", children: [
          /* @__PURE__ */ jsx("thead", { className: "bg-gray-50", children: /* @__PURE__ */ jsxs("tr", { children: [
            /* @__PURE__ */ jsxs(
              "th",
              {
                className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100",
                onClick: () => handleSort("nombre"),
                children: [
                  "Producto",
                  filters.sort_by === "nombre" && /* @__PURE__ */ jsx("span", { className: "ml-1", children: filters.sort_direction === "asc" ? "↑" : "↓" })
                ]
              }
            ),
            /* @__PURE__ */ jsx(
              "th",
              {
                className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100",
                onClick: () => handleSort("categoria"),
                children: "Categoría"
              }
            ),
            /* @__PURE__ */ jsx(
              "th",
              {
                className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100",
                onClick: () => handleSort("stock"),
                children: "Stock"
              }
            ),
            /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Precios" }),
            /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Estado" }),
            /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Valor" }),
            /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Acciones" })
          ] }) }),
          /* @__PURE__ */ jsx("tbody", { className: "bg-white divide-y divide-gray-200", children: products.data.map((product) => /* @__PURE__ */ jsxs("tr", { className: "hover:bg-gray-50", children: [
            /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsxs("div", { children: [
              /* @__PURE__ */ jsx("div", { className: "text-sm font-medium text-gray-900", children: product.nombre }),
              /* @__PURE__ */ jsx("div", { className: "text-sm text-gray-500", children: product.codigo_interno })
            ] }) }),
            /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap text-sm text-gray-900", children: product.categoria }),
            /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsxs("div", { className: "text-sm text-gray-900", children: [
              /* @__PURE__ */ jsxs("div", { children: [
                "Actual: ",
                /* @__PURE__ */ jsx("span", { className: "font-medium", children: product.stock_actual })
              ] }),
              product.stock_reservado > 0 && /* @__PURE__ */ jsxs("div", { className: "text-xs text-gray-500", children: [
                "Disponible: ",
                product.stock_disponible
              ] }),
              /* @__PURE__ */ jsxs("div", { className: "text-xs text-gray-500", children: [
                "Mín: ",
                product.stock_minimo,
                " | Máx: ",
                product.stock_maximo
              ] })
            ] }) }),
            /* @__PURE__ */ jsxs("td", { className: "px-6 py-4 whitespace-nowrap text-sm text-gray-900", children: [
              /* @__PURE__ */ jsxs("div", { children: [
                "Costo: $",
                product.precio_costo
              ] }),
              /* @__PURE__ */ jsxs("div", { children: [
                "Venta: $",
                product.precio_venta
              ] })
            ] }),
            /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsx("span", { className: `inline-flex px-2 py-1 text-xs font-semibold rounded-full ${stockStatusColors[product.estado_stock]}`, children: stockStatusLabels[product.estado_stock] }) }),
            /* @__PURE__ */ jsxs("td", { className: "px-6 py-4 whitespace-nowrap text-sm text-gray-900", children: [
              "$",
              product.valor_inventario.toLocaleString()
            ] }),
            /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap text-sm font-medium", children: /* @__PURE__ */ jsxs(
              Button,
              {
                variant: "outline",
                size: "sm",
                onClick: () => openAdjustModal(product),
                children: [
                  /* @__PURE__ */ jsx(AdjustmentsHorizontalIcon, { className: "h-4 w-4 mr-1" }),
                  "Ajustar"
                ]
              }
            ) })
          ] }, product.id)) })
        ] }) }),
        products.last_page > 1 && /* @__PURE__ */ jsx("div", { className: "bg-white px-4 py-3 border-t border-gray-200 sm:px-6", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
          /* @__PURE__ */ jsxs("div", { className: "text-sm text-gray-700", children: [
            "Mostrando ",
            (products.current_page - 1) * products.per_page + 1,
            " a ",
            Math.min(products.current_page * products.per_page, products.total),
            " de ",
            products.total,
            " productos"
          ] }),
          /* @__PURE__ */ jsx("div", { className: "flex space-x-2", children: products.links.map((link, index) => link.url && /* @__PURE__ */ jsx(
            Link,
            {
              href: link.url,
              className: `px-3 py-2 text-sm font-medium rounded-md ${link.active ? "bg-blue-600 text-white" : "text-gray-700 bg-white border border-gray-300 hover:bg-gray-50"}`,
              dangerouslySetInnerHTML: { __html: link.label }
            },
            index
          )) })
        ] }) })
      ] }),
      showAdjustModal && selectedProduct && /* @__PURE__ */ jsx("div", { className: "fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50", children: /* @__PURE__ */ jsx("div", { className: "relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white", children: /* @__PURE__ */ jsxs("div", { className: "mt-3", children: [
        /* @__PURE__ */ jsxs("h3", { className: "text-lg font-medium text-gray-900 mb-4", children: [
          "Ajustar Stock: ",
          selectedProduct.nombre
        ] }),
        /* @__PURE__ */ jsxs("div", { className: "mb-4 text-sm text-gray-600", children: [
          "Stock actual: ",
          selectedProduct.stock_actual
        ] }),
        /* @__PURE__ */ jsxs("div", { className: "space-y-4", children: [
          /* @__PURE__ */ jsxs("div", { children: [
            /* @__PURE__ */ jsx("label", { className: "block text-sm font-medium text-gray-700", children: "Tipo de ajuste" }),
            /* @__PURE__ */ jsxs(
              "select",
              {
                value: adjustmentData.tipo_ajuste,
                onChange: (e) => setAdjustmentData({
                  ...adjustmentData,
                  tipo_ajuste: e.target.value
                }),
                className: "mt-1 block w-full border border-gray-300 rounded-md px-3 py-2",
                children: [
                  /* @__PURE__ */ jsx("option", { value: "entrada", children: "Entrada (+)" }),
                  /* @__PURE__ */ jsx("option", { value: "salida", children: "Salida (-)" }),
                  /* @__PURE__ */ jsx("option", { value: "ajuste", children: "Ajuste (=)" })
                ]
              }
            )
          ] }),
          /* @__PURE__ */ jsxs("div", { children: [
            /* @__PURE__ */ jsx("label", { className: "block text-sm font-medium text-gray-700", children: "Cantidad" }),
            /* @__PURE__ */ jsx(
              "input",
              {
                type: "number",
                step: "0.01",
                min: "0",
                value: adjustmentData.cantidad,
                onChange: (e) => setAdjustmentData({ ...adjustmentData, cantidad: e.target.value }),
                className: "mt-1 block w-full border border-gray-300 rounded-md px-3 py-2",
                placeholder: "0.00"
              }
            )
          ] }),
          /* @__PURE__ */ jsxs("div", { children: [
            /* @__PURE__ */ jsx("label", { className: "block text-sm font-medium text-gray-700", children: "Motivo" }),
            /* @__PURE__ */ jsx(
              "input",
              {
                type: "text",
                value: adjustmentData.motivo,
                onChange: (e) => setAdjustmentData({ ...adjustmentData, motivo: e.target.value }),
                className: "mt-1 block w-full border border-gray-300 rounded-md px-3 py-2",
                placeholder: "Describe el motivo del ajuste"
              }
            )
          ] })
        ] }),
        /* @__PURE__ */ jsxs("div", { className: "flex space-x-3 mt-6", children: [
          /* @__PURE__ */ jsx(
            Button,
            {
              onClick: handleAdjustStock,
              disabled: !adjustmentData.cantidad || !adjustmentData.motivo,
              className: "flex-1",
              children: "Confirmar Ajuste"
            }
          ),
          /* @__PURE__ */ jsx(
            Button,
            {
              variant: "outline",
              onClick: () => setShowAdjustModal(false),
              className: "flex-1",
              children: "Cancelar"
            }
          )
        ] })
      ] }) }) })
    ] }) })
  ] });
}
export {
  Inventory as default
};
