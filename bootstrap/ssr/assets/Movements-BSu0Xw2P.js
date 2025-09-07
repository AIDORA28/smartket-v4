import { jsxs, jsx } from "react/jsx-runtime";
import { useState } from "react";
import { Head, Link, router } from "@inertiajs/react";
import { A as AuthenticatedLayout } from "./AuthenticatedLayout-BfjI-KKz.js";
import { B as Button, C as Card } from "./Button-DrUMbt6l.js";
import { ArrowLeftIcon, MagnifyingGlassIcon, AdjustmentsHorizontalIcon, ArrowDownIcon, ArrowUpIcon } from "@heroicons/react/24/outline";
import "clsx";
const movementTypeColors = {
  entrada: "bg-green-100 text-green-800",
  salida: "bg-red-100 text-red-800",
  ajuste: "bg-yellow-100 text-yellow-800"
};
const movementTypeLabels = {
  entrada: "Entrada",
  salida: "Salida",
  ajuste: "Ajuste"
};
const movementIcons = {
  entrada: ArrowUpIcon,
  salida: ArrowDownIcon,
  ajuste: AdjustmentsHorizontalIcon
};
function InventoryMovements({ movements, products, filters, sort, error }) {
  const [search, setSearch] = useState(filters.search || "");
  const [movementType, setMovementType] = useState(filters.tipo_movimiento || "");
  const [startDate, setStartDate] = useState(filters.fecha_inicio || "");
  const [endDate, setEndDate] = useState(filters.fecha_fin || "");
  const [selectedProduct, setSelectedProduct] = useState(filters.producto_id || "");
  if (error) {
    return /* @__PURE__ */ jsxs(AuthenticatedLayout, { children: [
      /* @__PURE__ */ jsx(Head, { title: "Movimientos de Inventario" }),
      /* @__PURE__ */ jsx("div", { className: "py-12", children: /* @__PURE__ */ jsx("div", { className: "max-w-7xl mx-auto sm:px-6 lg:px-8", children: /* @__PURE__ */ jsx("div", { className: "bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded", children: error }) }) })
    ] });
  }
  const handleSearch = () => {
    router.get("/inventario/movements", {
      search,
      tipo_movimiento: movementType,
      fecha_inicio: startDate,
      fecha_fin: endDate,
      producto_id: selectedProduct,
      sort_by: sort.sort_by,
      sort_direction: sort.sort_direction
    }, { preserveState: true });
  };
  const handleSort = (column) => {
    const direction = sort.sort_by === column && sort.sort_direction === "asc" ? "desc" : "asc";
    router.get("/inventario/movements", {
      ...filters,
      sort_by: column,
      sort_direction: direction
    }, { preserveState: true });
  };
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString("es-ES", {
      year: "numeric",
      month: "2-digit",
      day: "2-digit",
      hour: "2-digit",
      minute: "2-digit"
    });
  };
  return /* @__PURE__ */ jsxs(AuthenticatedLayout, { children: [
    /* @__PURE__ */ jsx(Head, { title: "Movimientos de Inventario" }),
    /* @__PURE__ */ jsx("div", { className: "py-12", children: /* @__PURE__ */ jsxs("div", { className: "max-w-7xl mx-auto sm:px-6 lg:px-8", children: [
      /* @__PURE__ */ jsxs("div", { className: "mb-8 flex items-center justify-between", children: [
        /* @__PURE__ */ jsxs("div", { children: [
          /* @__PURE__ */ jsx("h1", { className: "text-3xl font-bold text-gray-900", children: "Movimientos de Inventario" }),
          /* @__PURE__ */ jsx("p", { className: "mt-2 text-gray-600", children: "Historial detallado de movimientos de stock" })
        ] }),
        /* @__PURE__ */ jsx(Link, { href: "/inventario", children: /* @__PURE__ */ jsxs(Button, { variant: "outline", children: [
          /* @__PURE__ */ jsx(ArrowLeftIcon, { className: "h-4 w-4 mr-2" }),
          "Volver al Inventario"
        ] }) })
      ] }),
      /* @__PURE__ */ jsxs(Card, { className: "mb-6", children: [
        /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4", children: [
          /* @__PURE__ */ jsxs("div", { children: [
            /* @__PURE__ */ jsx("label", { className: "block text-sm font-medium text-gray-700 mb-1", children: "Buscar" }),
            /* @__PURE__ */ jsxs("div", { className: "relative", children: [
              /* @__PURE__ */ jsx(MagnifyingGlassIcon, { className: "absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" }),
              /* @__PURE__ */ jsx(
                "input",
                {
                  type: "text",
                  placeholder: "Buscar producto...",
                  value: search,
                  onChange: (e) => setSearch(e.target.value),
                  onKeyPress: (e) => e.key === "Enter" && handleSearch(),
                  className: "pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                }
              )
            ] })
          ] }),
          /* @__PURE__ */ jsxs("div", { children: [
            /* @__PURE__ */ jsx("label", { className: "block text-sm font-medium text-gray-700 mb-1", children: "Producto" }),
            /* @__PURE__ */ jsxs(
              "select",
              {
                value: selectedProduct,
                onChange: (e) => setSelectedProduct(e.target.value),
                className: "w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500",
                children: [
                  /* @__PURE__ */ jsx("option", { value: "", children: "Todos los productos" }),
                  products.map((product) => /* @__PURE__ */ jsx("option", { value: product.id, children: product.nombre }, product.id))
                ]
              }
            )
          ] }),
          /* @__PURE__ */ jsxs("div", { children: [
            /* @__PURE__ */ jsx("label", { className: "block text-sm font-medium text-gray-700 mb-1", children: "Tipo" }),
            /* @__PURE__ */ jsxs(
              "select",
              {
                value: movementType,
                onChange: (e) => setMovementType(e.target.value),
                className: "w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500",
                children: [
                  /* @__PURE__ */ jsx("option", { value: "", children: "Todos los tipos" }),
                  /* @__PURE__ */ jsx("option", { value: "entrada", children: "Entrada" }),
                  /* @__PURE__ */ jsx("option", { value: "salida", children: "Salida" }),
                  /* @__PURE__ */ jsx("option", { value: "ajuste", children: "Ajuste" })
                ]
              }
            )
          ] }),
          /* @__PURE__ */ jsxs("div", { children: [
            /* @__PURE__ */ jsx("label", { className: "block text-sm font-medium text-gray-700 mb-1", children: "Fecha Inicio" }),
            /* @__PURE__ */ jsx(
              "input",
              {
                type: "date",
                value: startDate,
                onChange: (e) => setStartDate(e.target.value),
                className: "w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
              }
            )
          ] }),
          /* @__PURE__ */ jsxs("div", { children: [
            /* @__PURE__ */ jsx("label", { className: "block text-sm font-medium text-gray-700 mb-1", children: "Fecha Fin" }),
            /* @__PURE__ */ jsx(
              "input",
              {
                type: "date",
                value: endDate,
                onChange: (e) => setEndDate(e.target.value),
                className: "w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
              }
            )
          ] })
        ] }),
        /* @__PURE__ */ jsx("div", { className: "mt-4 flex justify-end", children: /* @__PURE__ */ jsx(Button, { onClick: handleSearch, children: "Aplicar Filtros" }) })
      ] }),
      /* @__PURE__ */ jsxs(Card, { children: [
        /* @__PURE__ */ jsx("div", { className: "overflow-x-auto", children: /* @__PURE__ */ jsxs("table", { className: "min-w-full divide-y divide-gray-200", children: [
          /* @__PURE__ */ jsx("thead", { className: "bg-gray-50", children: /* @__PURE__ */ jsxs("tr", { children: [
            /* @__PURE__ */ jsxs(
              "th",
              {
                className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100",
                onClick: () => handleSort("fecha_movimiento"),
                children: [
                  "Fecha",
                  sort.sort_by === "fecha_movimiento" && /* @__PURE__ */ jsx("span", { className: "ml-1", children: sort.sort_direction === "asc" ? "↑" : "↓" })
                ]
              }
            ),
            /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Producto" }),
            /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Tipo" }),
            /* @__PURE__ */ jsx(
              "th",
              {
                className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100",
                onClick: () => handleSort("cantidad"),
                children: "Cantidad"
              }
            ),
            /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Stock" }),
            /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Costo Unit." }),
            /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Usuario" }),
            /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Observaciones" })
          ] }) }),
          /* @__PURE__ */ jsx("tbody", { className: "bg-white divide-y divide-gray-200", children: movements.data.map((movement) => {
            const Icon = movementIcons[movement.tipo_movimiento];
            return /* @__PURE__ */ jsxs("tr", { className: "hover:bg-gray-50", children: [
              /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap text-sm text-gray-900", children: formatDate(movement.fecha) }),
              /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900", children: movement.producto }),
              /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsxs("span", { className: `inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${movementTypeColors[movement.tipo_movimiento]}`, children: [
                /* @__PURE__ */ jsx(Icon, { className: "h-3 w-3 mr-1" }),
                movementTypeLabels[movement.tipo_movimiento]
              ] }) }),
              /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap text-sm text-gray-900", children: /* @__PURE__ */ jsxs("span", { className: `font-medium ${movement.tipo_movimiento === "entrada" ? "text-green-600" : movement.tipo_movimiento === "salida" ? "text-red-600" : "text-yellow-600"}`, children: [
                movement.tipo_movimiento === "entrada" ? "+" : movement.tipo_movimiento === "salida" ? "-" : "=",
                movement.cantidad
              ] }) }),
              /* @__PURE__ */ jsxs("td", { className: "px-6 py-4 whitespace-nowrap text-sm text-gray-500", children: [
                /* @__PURE__ */ jsxs("div", { children: [
                  /* @__PURE__ */ jsx("span", { className: "text-gray-400", children: "Anterior:" }),
                  " ",
                  movement.stock_anterior
                ] }),
                /* @__PURE__ */ jsxs("div", { children: [
                  /* @__PURE__ */ jsx("span", { className: "text-gray-400", children: "Nuevo:" }),
                  " ",
                  /* @__PURE__ */ jsx("span", { className: "font-medium text-gray-900", children: movement.stock_posterior })
                ] })
              ] }),
              /* @__PURE__ */ jsxs("td", { className: "px-6 py-4 whitespace-nowrap text-sm text-gray-900", children: [
                "$",
                movement.costo_unitario.toFixed(2)
              ] }),
              /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap text-sm text-gray-500", children: movement.usuario }),
              /* @__PURE__ */ jsx("td", { className: "px-6 py-4 text-sm text-gray-500 max-w-xs truncate", children: movement.observaciones || "-" })
            ] }, movement.id);
          }) })
        ] }) }),
        movements.data.length === 0 && /* @__PURE__ */ jsxs("div", { className: "text-center py-12", children: [
          /* @__PURE__ */ jsx(AdjustmentsHorizontalIcon, { className: "mx-auto h-12 w-12 text-gray-400" }),
          /* @__PURE__ */ jsx("h3", { className: "mt-2 text-sm font-medium text-gray-900", children: "No hay movimientos" }),
          /* @__PURE__ */ jsx("p", { className: "mt-1 text-sm text-gray-500", children: "No se encontraron movimientos con los filtros aplicados." })
        ] }),
        movements.last_page > 1 && /* @__PURE__ */ jsx("div", { className: "bg-white px-4 py-3 border-t border-gray-200 sm:px-6", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
          /* @__PURE__ */ jsxs("div", { className: "text-sm text-gray-700", children: [
            "Mostrando ",
            (movements.current_page - 1) * movements.per_page + 1,
            " a ",
            Math.min(movements.current_page * movements.per_page, movements.total),
            " de ",
            movements.total,
            " movimientos"
          ] }),
          /* @__PURE__ */ jsx("div", { className: "flex space-x-2", children: movements.links.map((link, index) => link.url && /* @__PURE__ */ jsx(
            Link,
            {
              href: link.url,
              className: `px-3 py-2 text-sm font-medium rounded-md ${link.active ? "bg-blue-600 text-white" : "text-gray-700 bg-white border border-gray-300 hover:bg-gray-50"}`,
              dangerouslySetInnerHTML: { __html: link.label }
            },
            index
          )) })
        ] }) })
      ] })
    ] }) })
  ] });
}
export {
  InventoryMovements as default
};
