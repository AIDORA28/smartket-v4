import { jsxs, jsx } from "react/jsx-runtime";
import { useState } from "react";
import { Head, router } from "@inertiajs/react";
import { clsx } from "clsx";
import { A as AuthenticatedLayout } from "./AuthenticatedLayout-BfjI-KKz.js";
import { C as Card, a as CardHeader, b as CardBody, B as Button } from "./Card-DoF5IT7l.js";
import { CurrencyDollarIcon, ShoppingCartIcon, CalendarIcon, BanknotesIcon, CreditCardIcon, AdjustmentsHorizontalIcon, MagnifyingGlassIcon, UserIcon, EyeIcon, PrinterIcon, XMarkIcon, CloudArrowDownIcon } from "@heroicons/react/24/outline";
function Sales({ auth, sales, stats, filters }) {
  const [searchTerm, setSearchTerm] = useState(filters.search || "");
  const [dateFrom, setDateFrom] = useState(filters.date_from || "");
  const [dateTo, setDateTo] = useState(filters.date_to || "");
  const [paymentMethod, setPaymentMethod] = useState(filters.payment_method || "all");
  const [status, setStatus] = useState(filters.status || "all");
  const [selectedSale, setSelectedSale] = useState(null);
  const filteredSales = sales.filter((sale) => {
    var _a;
    const matchesSearch = sale.numero_venta.includes(searchTerm) || ((_a = sale.cliente_nombre) == null ? void 0 : _a.toLowerCase().includes(searchTerm.toLowerCase())) || sale.cajero.toLowerCase().includes(searchTerm.toLowerCase());
    const saleDate = new Date(sale.created_at);
    const matchesDateFrom = !dateFrom || saleDate >= new Date(dateFrom);
    const matchesDateTo = !dateTo || saleDate <= /* @__PURE__ */ new Date(dateTo + " 23:59:59");
    const matchesPayment = paymentMethod === "all" || sale.metodo_pago === paymentMethod;
    const matchesStatus = status === "all" || sale.estado === status;
    return matchesSearch && matchesDateFrom && matchesDateTo && matchesPayment && matchesStatus;
  });
  const getStatusColor = (status2) => {
    switch (status2) {
      case "completed":
        return "bg-green-100 text-green-800";
      case "pending":
        return "bg-yellow-100 text-yellow-800";
      case "cancelled":
        return "bg-red-100 text-red-800";
      default:
        return "bg-gray-100 text-gray-800";
    }
  };
  const getStatusText = (status2) => {
    switch (status2) {
      case "completed":
        return "Completada";
      case "pending":
        return "Pendiente";
      case "cancelled":
        return "Cancelada";
      default:
        return status2;
    }
  };
  const applyFilters = () => {
    router.get("/sales", {
      search: searchTerm,
      date_from: dateFrom,
      date_to: dateTo,
      payment_method: paymentMethod === "all" ? void 0 : paymentMethod,
      status: status === "all" ? void 0 : status
    }, {
      preserveState: true,
      only: ["sales"]
    });
  };
  const printSale = (saleId) => {
    router.get(`/sales/${saleId}/print`);
  };
  const cancelSale = (saleId) => {
    if (confirm("¿Estás seguro de cancelar esta venta?")) {
      router.patch(`/sales/${saleId}/cancel`, {}, {
        onSuccess: () => {
        }
      });
    }
  };
  return /* @__PURE__ */ jsxs(
    AuthenticatedLayout,
    {
      header: /* @__PURE__ */ jsxs("div", { className: "flex justify-between items-center", children: [
        /* @__PURE__ */ jsxs("div", { children: [
          /* @__PURE__ */ jsx("h2", { className: "font-semibold text-xl text-gray-800 leading-tight", children: "Historial de Ventas" }),
          /* @__PURE__ */ jsx("p", { className: "text-gray-600 text-sm mt-1", children: "Registro completo de todas las transacciones" })
        ] }),
        /* @__PURE__ */ jsx("div", { className: "flex gap-3", children: /* @__PURE__ */ jsxs(Button, { variant: "secondary", size: "sm", children: [
          /* @__PURE__ */ jsx(CloudArrowDownIcon, { className: "w-4 h-4 mr-2" }),
          "Exportar"
        ] }) })
      ] }),
      children: [
        /* @__PURE__ */ jsx(Head, { title: "Ventas" }),
        /* @__PURE__ */ jsx("div", { className: "py-6", children: /* @__PURE__ */ jsxs("div", { className: "max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6", children: [
          /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4", children: [
            /* @__PURE__ */ jsx("div", { className: "bg-blue-50 p-4 rounded-lg border", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
              /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsxs("div", { className: "text-2xl font-bold text-blue-600", children: [
                  "$",
                  stats.total_ventas.toLocaleString()
                ] }),
                /* @__PURE__ */ jsx("div", { className: "text-sm text-blue-700", children: "Total Ventas" })
              ] }),
              /* @__PURE__ */ jsx(CurrencyDollarIcon, { className: "w-8 h-8 text-blue-500" })
            ] }) }),
            /* @__PURE__ */ jsx("div", { className: "bg-green-50 p-4 rounded-lg border", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
              /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsx("div", { className: "text-2xl font-bold text-green-600", children: stats.total_cantidad }),
                /* @__PURE__ */ jsx("div", { className: "text-sm text-green-700", children: "Transacciones" })
              ] }),
              /* @__PURE__ */ jsx(ShoppingCartIcon, { className: "w-8 h-8 text-green-500" })
            ] }) }),
            /* @__PURE__ */ jsx("div", { className: "bg-purple-50 p-4 rounded-lg border", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
              /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsxs("div", { className: "text-2xl font-bold text-purple-600", children: [
                  "$",
                  stats.promedio_ticket.toLocaleString()
                ] }),
                /* @__PURE__ */ jsx("div", { className: "text-sm text-purple-700", children: "Ticket Promedio" })
              ] }),
              /* @__PURE__ */ jsx(CalendarIcon, { className: "w-8 h-8 text-purple-500" })
            ] }) }),
            /* @__PURE__ */ jsx("div", { className: "bg-green-50 p-4 rounded-lg border", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
              /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsxs("div", { className: "text-2xl font-bold text-green-600", children: [
                  "$",
                  stats.ventas_efectivo.toLocaleString()
                ] }),
                /* @__PURE__ */ jsx("div", { className: "text-sm text-green-700", children: "Efectivo" })
              ] }),
              /* @__PURE__ */ jsx(BanknotesIcon, { className: "w-8 h-8 text-green-500" })
            ] }) }),
            /* @__PURE__ */ jsx("div", { className: "bg-blue-50 p-4 rounded-lg border", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
              /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsxs("div", { className: "text-2xl font-bold text-blue-600", children: [
                  "$",
                  stats.ventas_tarjeta.toLocaleString()
                ] }),
                /* @__PURE__ */ jsx("div", { className: "text-sm text-blue-700", children: "Tarjeta" })
              ] }),
              /* @__PURE__ */ jsx(CreditCardIcon, { className: "w-8 h-8 text-blue-500" })
            ] }) })
          ] }),
          /* @__PURE__ */ jsxs(Card, { children: [
            /* @__PURE__ */ jsx(CardHeader, { children: /* @__PURE__ */ jsxs("div", { className: "flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4", children: [
              /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900", children: "Filtros y Búsqueda" }),
              /* @__PURE__ */ jsxs("div", { className: "flex items-center gap-2", children: [
                /* @__PURE__ */ jsx(AdjustmentsHorizontalIcon, { className: "w-5 h-5 text-gray-500" }),
                /* @__PURE__ */ jsxs("span", { className: "text-sm text-gray-500", children: [
                  filteredSales.length,
                  " de ",
                  sales.length,
                  " ventas"
                ] })
              ] })
            ] }) }),
            /* @__PURE__ */ jsxs(CardBody, { children: [
              /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4", children: [
                /* @__PURE__ */ jsxs("div", { className: "relative", children: [
                  /* @__PURE__ */ jsx(MagnifyingGlassIcon, { className: "absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" }),
                  /* @__PURE__ */ jsx(
                    "input",
                    {
                      type: "text",
                      placeholder: "Buscar ventas...",
                      className: "pl-10 pr-4 py-2 w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500",
                      value: searchTerm,
                      onChange: (e) => setSearchTerm(e.target.value)
                    }
                  )
                ] }),
                /* @__PURE__ */ jsx(
                  "input",
                  {
                    type: "date",
                    className: "px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500",
                    value: dateFrom,
                    onChange: (e) => setDateFrom(e.target.value)
                  }
                ),
                /* @__PURE__ */ jsx(
                  "input",
                  {
                    type: "date",
                    className: "px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500",
                    value: dateTo,
                    onChange: (e) => setDateTo(e.target.value)
                  }
                ),
                /* @__PURE__ */ jsxs(
                  "select",
                  {
                    className: "px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500",
                    value: paymentMethod,
                    onChange: (e) => setPaymentMethod(e.target.value),
                    children: [
                      /* @__PURE__ */ jsx("option", { value: "all", children: "Todos los pagos" }),
                      /* @__PURE__ */ jsx("option", { value: "cash", children: "Efectivo" }),
                      /* @__PURE__ */ jsx("option", { value: "card", children: "Tarjeta" })
                    ]
                  }
                ),
                /* @__PURE__ */ jsxs(
                  "select",
                  {
                    className: "px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500",
                    value: status,
                    onChange: (e) => setStatus(e.target.value),
                    children: [
                      /* @__PURE__ */ jsx("option", { value: "all", children: "Todos los estados" }),
                      /* @__PURE__ */ jsx("option", { value: "completed", children: "Completadas" }),
                      /* @__PURE__ */ jsx("option", { value: "pending", children: "Pendientes" }),
                      /* @__PURE__ */ jsx("option", { value: "cancelled", children: "Canceladas" })
                    ]
                  }
                )
              ] }),
              /* @__PURE__ */ jsx(
                Button,
                {
                  variant: "primary",
                  onClick: applyFilters,
                  className: "w-full sm:w-auto",
                  children: "Aplicar Filtros"
                }
              )
            ] })
          ] }),
          /* @__PURE__ */ jsxs(Card, { children: [
            /* @__PURE__ */ jsx(CardHeader, { children: /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900", children: "Lista de Ventas" }) }),
            /* @__PURE__ */ jsx(CardBody, { children: /* @__PURE__ */ jsxs("div", { className: "overflow-x-auto", children: [
              /* @__PURE__ */ jsxs("table", { className: "min-w-full divide-y divide-gray-200", children: [
                /* @__PURE__ */ jsx("thead", { className: "bg-gray-50", children: /* @__PURE__ */ jsxs("tr", { children: [
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "# Venta" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Cliente" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Total" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Pago" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Estado" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Fecha" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Acciones" })
                ] }) }),
                /* @__PURE__ */ jsx("tbody", { className: "bg-white divide-y divide-gray-200", children: filteredSales.map((sale) => /* @__PURE__ */ jsxs("tr", { className: "hover:bg-gray-50", children: [
                  /* @__PURE__ */ jsxs("td", { className: "px-6 py-4 whitespace-nowrap", children: [
                    /* @__PURE__ */ jsx("div", { className: "text-sm font-medium text-blue-600", children: sale.numero_venta }),
                    /* @__PURE__ */ jsxs("div", { className: "text-xs text-gray-500", children: [
                      sale.items.length,
                      " productos"
                    ] })
                  ] }),
                  /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center", children: [
                    /* @__PURE__ */ jsx(UserIcon, { className: "w-4 h-4 text-gray-400 mr-2" }),
                    /* @__PURE__ */ jsxs("div", { children: [
                      /* @__PURE__ */ jsx("div", { className: "text-sm text-gray-900", children: sale.cliente_nombre || "Cliente General" }),
                      /* @__PURE__ */ jsxs("div", { className: "text-xs text-gray-500", children: [
                        "Cajero: ",
                        sale.cajero
                      ] })
                    ] })
                  ] }) }),
                  /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsxs("div", { className: "text-lg font-semibold text-gray-900", children: [
                    "$",
                    sale.total.toLocaleString()
                  ] }) }),
                  /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center", children: [
                    sale.metodo_pago === "cash" ? /* @__PURE__ */ jsx(BanknotesIcon, { className: "w-4 h-4 text-green-500 mr-2" }) : /* @__PURE__ */ jsx(CreditCardIcon, { className: "w-4 h-4 text-blue-500 mr-2" }),
                    /* @__PURE__ */ jsx("span", { className: "text-sm text-gray-900", children: sale.metodo_pago === "cash" ? "Efectivo" : "Tarjeta" })
                  ] }) }),
                  /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsx("span", { className: clsx(
                    "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium",
                    getStatusColor(sale.estado)
                  ), children: getStatusText(sale.estado) }) }),
                  /* @__PURE__ */ jsxs("td", { className: "px-6 py-4 whitespace-nowrap", children: [
                    /* @__PURE__ */ jsx("div", { className: "text-sm text-gray-900", children: new Date(sale.created_at).toLocaleDateString() }),
                    /* @__PURE__ */ jsx("div", { className: "text-xs text-gray-500", children: new Date(sale.created_at).toLocaleTimeString() })
                  ] }),
                  /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap text-right text-sm font-medium", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-end gap-2", children: [
                    /* @__PURE__ */ jsx(
                      Button,
                      {
                        variant: "ghost",
                        size: "sm",
                        onClick: () => setSelectedSale(sale),
                        children: /* @__PURE__ */ jsx(EyeIcon, { className: "w-4 h-4" })
                      }
                    ),
                    /* @__PURE__ */ jsx(
                      Button,
                      {
                        variant: "ghost",
                        size: "sm",
                        onClick: () => printSale(sale.id),
                        children: /* @__PURE__ */ jsx(PrinterIcon, { className: "w-4 h-4" })
                      }
                    ),
                    sale.estado === "completed" && /* @__PURE__ */ jsx(
                      Button,
                      {
                        variant: "ghost",
                        size: "sm",
                        className: "text-red-600 hover:text-red-700",
                        onClick: () => cancelSale(sale.id),
                        children: /* @__PURE__ */ jsx(XMarkIcon, { className: "w-4 h-4" })
                      }
                    )
                  ] }) })
                ] }, sale.id)) })
              ] }),
              filteredSales.length === 0 && /* @__PURE__ */ jsxs("div", { className: "text-center py-12", children: [
                /* @__PURE__ */ jsx("div", { className: "text-gray-400 text-6xl mb-4", children: "🛒" }),
                /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900 mb-2", children: "No se encontraron ventas" }),
                /* @__PURE__ */ jsx("p", { className: "text-gray-500 mb-6", children: searchTerm || dateFrom || dateTo || paymentMethod !== "all" || status !== "all" ? "Intenta ajustar los filtros de búsqueda" : "Aún no hay ventas registradas" })
              ] })
            ] }) })
          ] })
        ] }) }),
        selectedSale && /* @__PURE__ */ jsx("div", { className: "fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50", children: /* @__PURE__ */ jsxs("div", { className: "relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white", children: [
          /* @__PURE__ */ jsxs("div", { className: "flex justify-between items-center mb-4", children: [
            /* @__PURE__ */ jsxs("h3", { className: "text-lg font-medium text-gray-900", children: [
              "Detalle de Venta #",
              selectedSale.numero_venta
            ] }),
            /* @__PURE__ */ jsx(
              "button",
              {
                onClick: () => setSelectedSale(null),
                className: "text-gray-400 hover:text-gray-600",
                children: /* @__PURE__ */ jsx(XMarkIcon, { className: "w-6 h-6" })
              }
            )
          ] }),
          /* @__PURE__ */ jsxs("div", { className: "space-y-4", children: [
            /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-2 gap-4", children: [
              /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-500", children: "Cliente" }),
                /* @__PURE__ */ jsx("p", { className: "text-sm text-gray-900", children: selectedSale.cliente_nombre || "Cliente General" })
              ] }),
              /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-500", children: "Cajero" }),
                /* @__PURE__ */ jsx("p", { className: "text-sm text-gray-900", children: selectedSale.cajero })
              ] }),
              /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-500", children: "Método de Pago" }),
                /* @__PURE__ */ jsx("p", { className: "text-sm text-gray-900", children: selectedSale.metodo_pago === "cash" ? "Efectivo" : "Tarjeta" })
              ] }),
              /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-500", children: "Estado" }),
                /* @__PURE__ */ jsx("span", { className: clsx(
                  "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium",
                  getStatusColor(selectedSale.estado)
                ), children: getStatusText(selectedSale.estado) })
              ] })
            ] }),
            /* @__PURE__ */ jsxs("div", { children: [
              /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-500 mb-3", children: "Productos" }),
              /* @__PURE__ */ jsx("div", { className: "space-y-2", children: selectedSale.items.map((item) => /* @__PURE__ */ jsxs("div", { className: "flex justify-between items-center p-2 bg-gray-50 rounded", children: [
                /* @__PURE__ */ jsxs("div", { children: [
                  /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-900", children: item.producto_nombre }),
                  /* @__PURE__ */ jsxs("p", { className: "text-xs text-gray-500", children: [
                    item.cantidad,
                    " x $",
                    item.precio.toLocaleString()
                  ] })
                ] }),
                /* @__PURE__ */ jsxs("div", { className: "text-sm font-medium text-gray-900", children: [
                  "$",
                  item.subtotal.toLocaleString()
                ] })
              ] }, item.id)) })
            ] }),
            /* @__PURE__ */ jsx("div", { className: "border-t pt-4", children: /* @__PURE__ */ jsxs("div", { className: "flex justify-between items-center text-lg font-semibold", children: [
              /* @__PURE__ */ jsx("span", { children: "Total" }),
              /* @__PURE__ */ jsxs("span", { children: [
                "$",
                selectedSale.total.toLocaleString()
              ] })
            ] }) })
          ] }),
          /* @__PURE__ */ jsxs("div", { className: "mt-6 flex justify-end gap-3", children: [
            /* @__PURE__ */ jsxs(
              Button,
              {
                variant: "secondary",
                onClick: () => printSale(selectedSale.id),
                children: [
                  /* @__PURE__ */ jsx(PrinterIcon, { className: "w-4 h-4 mr-2" }),
                  "Imprimir"
                ]
              }
            ),
            /* @__PURE__ */ jsx(
              Button,
              {
                variant: "primary",
                onClick: () => setSelectedSale(null),
                children: "Cerrar"
              }
            )
          ] })
        ] }) })
      ]
    }
  );
}
export {
  Sales as default
};
