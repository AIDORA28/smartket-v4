import { jsxs, jsx } from "react/jsx-runtime";
import { useState } from "react";
import { Head, router } from "@inertiajs/react";
import { clsx } from "clsx";
import { A as AuthenticatedLayout } from "./AuthenticatedLayout-BfjI-KKz.js";
import { C as Card, a as CardHeader, B as Button, b as CardBody } from "./Card-DoF5IT7l.js";
import { S as StatsCard } from "./StatsCard-DHQ101IL.js";
import { CloudArrowDownIcon, CurrencyDollarIcon, ChartBarIcon, CalendarIcon, ShoppingCartIcon, ArrowTrendingUpIcon, ArrowTrendingDownIcon, ArrowPathIcon } from "@heroicons/react/24/outline";
function Reports({ auth, reportData, filters }) {
  const [startDate, setStartDate] = useState(filters.fecha_inicio);
  const [endDate, setEndDate] = useState(filters.fecha_fin);
  const [loading, setLoading] = useState(false);
  const updateFilters = () => {
    setLoading(true);
    router.get("/reports", {
      fecha_inicio: startDate,
      fecha_fin: endDate
    }, {
      preserveState: true,
      onFinish: () => setLoading(false)
    });
  };
  const exportReport = (type) => {
    router.get(`/reports/export/${type}`, {
      fecha_inicio: startDate,
      fecha_fin: endDate
    });
  };
  const formatCurrency = (amount) => `$${amount.toLocaleString()}`;
  const formatPercent = (value) => `${value > 0 ? "+" : ""}${value.toFixed(1)}%`;
  return /* @__PURE__ */ jsxs(
    AuthenticatedLayout,
    {
      header: /* @__PURE__ */ jsxs("div", { className: "flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4", children: [
        /* @__PURE__ */ jsxs("div", { children: [
          /* @__PURE__ */ jsx("h2", { className: "font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2", children: "📊 Reportes y Analytics" }),
          /* @__PURE__ */ jsx("p", { className: "text-gray-600 mt-1", children: "Dashboard ejecutivo y suite de reportes visuales" })
        ] }),
        /* @__PURE__ */ jsxs("div", { className: "flex flex-col sm:flex-row gap-3", children: [
          /* @__PURE__ */ jsxs("div", { className: "flex gap-2", children: [
            /* @__PURE__ */ jsx(
              "input",
              {
                type: "date",
                value: startDate,
                onChange: (e) => setStartDate(e.target.value),
                className: "px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              }
            ),
            /* @__PURE__ */ jsx(
              "input",
              {
                type: "date",
                value: endDate,
                onChange: (e) => setEndDate(e.target.value),
                className: "px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              }
            )
          ] }),
          /* @__PURE__ */ jsxs(
            Button,
            {
              variant: "primary",
              size: "sm",
              onClick: updateFilters,
              disabled: loading,
              children: [
                loading ? /* @__PURE__ */ jsx(ArrowPathIcon, { className: "w-4 h-4 mr-2 animate-spin" }) : /* @__PURE__ */ jsx(ArrowPathIcon, { className: "w-4 h-4 mr-2" }),
                loading ? "Actualizando..." : "Actualizar"
              ]
            }
          )
        ] })
      ] }),
      children: [
        /* @__PURE__ */ jsx(Head, { title: "Reportes" }),
        /* @__PURE__ */ jsx("div", { className: "py-6", children: /* @__PURE__ */ jsxs("div", { className: "max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6", children: [
          /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6", children: [
            /* @__PURE__ */ jsx(
              StatsCard,
              {
                title: "Ventas Totales",
                value: formatCurrency(reportData.ventas.total),
                icon: "money",
                trend: reportData.ventas.variacion !== 0 ? {
                  value: Math.abs(reportData.ventas.variacion),
                  label: `${formatPercent(reportData.ventas.variacion)} vs período anterior`,
                  direction: reportData.ventas.variacion > 0 ? "up" : "down"
                } : void 0,
                color: "green"
              }
            ),
            /* @__PURE__ */ jsx(
              StatsCard,
              {
                title: "Número de Ventas",
                value: reportData.ventas.cantidad,
                icon: "cart",
                color: "blue"
              }
            ),
            /* @__PURE__ */ jsx(
              StatsCard,
              {
                title: "Ticket Promedio",
                value: formatCurrency(reportData.ventas.promedio),
                icon: "money",
                color: "purple"
              }
            ),
            /* @__PURE__ */ jsx(
              StatsCard,
              {
                title: "Clientes Nuevos",
                value: reportData.clientes.nuevos,
                icon: "users",
                color: "green"
              }
            )
          ] }),
          /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 lg:grid-cols-2 gap-6", children: [
            /* @__PURE__ */ jsxs(Card, { children: [
              /* @__PURE__ */ jsx(CardHeader, { children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
                /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900", children: "Productos Más Vendidos" }),
                /* @__PURE__ */ jsx(
                  Button,
                  {
                    variant: "ghost",
                    size: "sm",
                    onClick: () => exportReport("productos-mas-vendidos"),
                    children: /* @__PURE__ */ jsx(CloudArrowDownIcon, { className: "w-4 h-4" })
                  }
                )
              ] }) }),
              /* @__PURE__ */ jsx(CardBody, { children: /* @__PURE__ */ jsxs("div", { className: "space-y-4", children: [
                reportData.productos.mas_vendidos.map((product, index) => /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between p-3 bg-gray-50 rounded-lg", children: [
                  /* @__PURE__ */ jsxs("div", { className: "flex items-center", children: [
                    /* @__PURE__ */ jsx("div", { className: "w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3", children: /* @__PURE__ */ jsxs("span", { className: "text-blue-600 font-bold text-sm", children: [
                      "#",
                      index + 1
                    ] }) }),
                    /* @__PURE__ */ jsxs("div", { children: [
                      /* @__PURE__ */ jsx("p", { className: "font-medium text-gray-900", children: product.nombre }),
                      /* @__PURE__ */ jsxs("p", { className: "text-sm text-gray-500", children: [
                        product.cantidad,
                        " unidades vendidas"
                      ] })
                    ] })
                  ] }),
                  /* @__PURE__ */ jsx("div", { className: "text-right", children: /* @__PURE__ */ jsx("p", { className: "font-semibold text-green-600", children: formatCurrency(product.total) }) })
                ] }, product.id)),
                reportData.productos.mas_vendidos.length === 0 && /* @__PURE__ */ jsx("div", { className: "text-center py-8 text-gray-500", children: /* @__PURE__ */ jsx("p", { children: "No hay datos de productos vendidos en este período" }) })
              ] }) })
            ] }),
            /* @__PURE__ */ jsxs(Card, { children: [
              /* @__PURE__ */ jsx(CardHeader, { children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
                /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900", children: "Top Compradores" }),
                /* @__PURE__ */ jsx(
                  Button,
                  {
                    variant: "ghost",
                    size: "sm",
                    onClick: () => exportReport("top-compradores"),
                    children: /* @__PURE__ */ jsx(CloudArrowDownIcon, { className: "w-4 h-4" })
                  }
                )
              ] }) }),
              /* @__PURE__ */ jsx(CardBody, { children: /* @__PURE__ */ jsxs("div", { className: "space-y-4", children: [
                reportData.clientes.top_compradores.map((client, index) => /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between p-3 bg-gray-50 rounded-lg", children: [
                  /* @__PURE__ */ jsxs("div", { className: "flex items-center", children: [
                    /* @__PURE__ */ jsx("div", { className: "w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3", children: /* @__PURE__ */ jsxs("span", { className: "text-purple-600 font-bold text-sm", children: [
                      "#",
                      index + 1
                    ] }) }),
                    /* @__PURE__ */ jsxs("div", { children: [
                      /* @__PURE__ */ jsx("p", { className: "font-medium text-gray-900", children: client.nombre }),
                      /* @__PURE__ */ jsxs("p", { className: "text-sm text-gray-500", children: [
                        client.compras,
                        " compras realizadas"
                      ] })
                    ] })
                  ] }),
                  /* @__PURE__ */ jsx("div", { className: "text-right", children: /* @__PURE__ */ jsx("p", { className: "font-semibold text-purple-600", children: formatCurrency(client.total) }) })
                ] }, client.id)),
                reportData.clientes.top_compradores.length === 0 && /* @__PURE__ */ jsx("div", { className: "text-center py-8 text-gray-500", children: /* @__PURE__ */ jsx("p", { children: "No hay datos de clientes en este período" }) })
              ] }) })
            ] })
          ] }),
          /* @__PURE__ */ jsxs(Card, { children: [
            /* @__PURE__ */ jsx(CardHeader, { children: /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900", children: "Métodos de Pago" }) }),
            /* @__PURE__ */ jsx(CardBody, { children: /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 md:grid-cols-2 gap-6", children: [
              /* @__PURE__ */ jsxs("div", { className: "bg-green-50 p-6 rounded-lg", children: [
                /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between mb-4", children: [
                  /* @__PURE__ */ jsx("h4", { className: "font-medium text-green-900", children: "Efectivo" }),
                  /* @__PURE__ */ jsx("div", { className: "w-10 h-10 bg-green-100 rounded-full flex items-center justify-center", children: /* @__PURE__ */ jsx(CurrencyDollarIcon, { className: "w-6 h-6 text-green-600" }) })
                ] }),
                /* @__PURE__ */ jsxs("div", { className: "space-y-2", children: [
                  /* @__PURE__ */ jsxs("div", { className: "flex justify-between", children: [
                    /* @__PURE__ */ jsx("span", { className: "text-green-700", children: "Total:" }),
                    /* @__PURE__ */ jsx("span", { className: "font-bold text-green-900", children: formatCurrency(reportData.metodos_pago.efectivo.total) })
                  ] }),
                  /* @__PURE__ */ jsxs("div", { className: "flex justify-between", children: [
                    /* @__PURE__ */ jsx("span", { className: "text-green-700", children: "Transacciones:" }),
                    /* @__PURE__ */ jsx("span", { className: "font-semibold text-green-800", children: reportData.metodos_pago.efectivo.cantidad })
                  ] })
                ] })
              ] }),
              /* @__PURE__ */ jsxs("div", { className: "bg-blue-50 p-6 rounded-lg", children: [
                /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between mb-4", children: [
                  /* @__PURE__ */ jsx("h4", { className: "font-medium text-blue-900", children: "Tarjeta" }),
                  /* @__PURE__ */ jsx("div", { className: "w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center", children: /* @__PURE__ */ jsx(ChartBarIcon, { className: "w-6 h-6 text-blue-600" }) })
                ] }),
                /* @__PURE__ */ jsxs("div", { className: "space-y-2", children: [
                  /* @__PURE__ */ jsxs("div", { className: "flex justify-between", children: [
                    /* @__PURE__ */ jsx("span", { className: "text-blue-700", children: "Total:" }),
                    /* @__PURE__ */ jsx("span", { className: "font-bold text-blue-900", children: formatCurrency(reportData.metodos_pago.tarjeta.total) })
                  ] }),
                  /* @__PURE__ */ jsxs("div", { className: "flex justify-between", children: [
                    /* @__PURE__ */ jsx("span", { className: "text-blue-700", children: "Transacciones:" }),
                    /* @__PURE__ */ jsx("span", { className: "font-semibold text-blue-800", children: reportData.metodos_pago.tarjeta.cantidad })
                  ] })
                ] })
              ] })
            ] }) })
          ] }),
          /* @__PURE__ */ jsxs(Card, { children: [
            /* @__PURE__ */ jsx(CardHeader, { children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
              /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900", children: "Resumen Diario" }),
              /* @__PURE__ */ jsxs("div", { className: "flex gap-2", children: [
                /* @__PURE__ */ jsxs(
                  Button,
                  {
                    variant: "ghost",
                    size: "sm",
                    onClick: () => exportReport("resumen-diario"),
                    children: [
                      /* @__PURE__ */ jsx(CloudArrowDownIcon, { className: "w-4 h-4 mr-2" }),
                      "Excel"
                    ]
                  }
                ),
                /* @__PURE__ */ jsxs(
                  Button,
                  {
                    variant: "ghost",
                    size: "sm",
                    onClick: () => exportReport("resumen-diario-pdf"),
                    children: [
                      /* @__PURE__ */ jsx(CloudArrowDownIcon, { className: "w-4 h-4 mr-2" }),
                      "PDF"
                    ]
                  }
                )
              ] })
            ] }) }),
            /* @__PURE__ */ jsx(CardBody, { children: /* @__PURE__ */ jsxs("div", { className: "overflow-x-auto", children: [
              /* @__PURE__ */ jsxs("table", { className: "min-w-full divide-y divide-gray-200", children: [
                /* @__PURE__ */ jsx("thead", { className: "bg-gray-50", children: /* @__PURE__ */ jsxs("tr", { children: [
                  /* @__PURE__ */ jsxs("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: [
                    /* @__PURE__ */ jsx(CalendarIcon, { className: "w-4 h-4 inline mr-2" }),
                    "Fecha"
                  ] }),
                  /* @__PURE__ */ jsxs("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: [
                    /* @__PURE__ */ jsx(ShoppingCartIcon, { className: "w-4 h-4 inline mr-2" }),
                    "Ventas"
                  ] }),
                  /* @__PURE__ */ jsxs("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: [
                    /* @__PURE__ */ jsx(CurrencyDollarIcon, { className: "w-4 h-4 inline mr-2" }),
                    "Total"
                  ] }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Tendencia" })
                ] }) }),
                /* @__PURE__ */ jsx("tbody", { className: "bg-white divide-y divide-gray-200", children: reportData.resumen_diario.map((day, index) => {
                  const prevDay = reportData.resumen_diario[index - 1];
                  const trend = prevDay ? day.ventas - prevDay.ventas : 0;
                  return /* @__PURE__ */ jsxs("tr", { className: "hover:bg-gray-50", children: [
                    /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap text-sm text-gray-900", children: new Date(day.fecha).toLocaleDateString("es-ES", {
                      weekday: "short",
                      day: "2-digit",
                      month: "2-digit"
                    }) }),
                    /* @__PURE__ */ jsxs("td", { className: "px-6 py-4 whitespace-nowrap text-sm text-gray-900", children: [
                      day.cantidad,
                      " ventas"
                    ] }),
                    /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900", children: formatCurrency(day.ventas) }),
                    /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap text-sm", children: trend !== 0 && /* @__PURE__ */ jsxs("div", { className: clsx(
                      "flex items-center",
                      trend > 0 ? "text-green-600" : "text-red-600"
                    ), children: [
                      trend > 0 ? /* @__PURE__ */ jsx(ArrowTrendingUpIcon, { className: "w-4 h-4 mr-1" }) : /* @__PURE__ */ jsx(ArrowTrendingDownIcon, { className: "w-4 h-4 mr-1" }),
                      formatCurrency(Math.abs(trend))
                    ] }) })
                  ] }, day.fecha);
                }) })
              ] }),
              reportData.resumen_diario.length === 0 && /* @__PURE__ */ jsxs("div", { className: "text-center py-12", children: [
                /* @__PURE__ */ jsx("div", { className: "text-gray-400 text-6xl mb-4", children: "📊" }),
                /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900 mb-2", children: "No hay datos disponibles" }),
                /* @__PURE__ */ jsx("p", { className: "text-gray-500", children: "Selecciona un rango de fechas diferente para ver los reportes" })
              ] })
            ] }) })
          ] }),
          /* @__PURE__ */ jsxs(Card, { children: [
            /* @__PURE__ */ jsx(CardHeader, { children: /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900", children: "Exportar Reportes Completos" }) }),
            /* @__PURE__ */ jsx(CardBody, { children: /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4", children: [
              /* @__PURE__ */ jsxs(
                Button,
                {
                  variant: "secondary",
                  onClick: () => exportReport("completo-excel"),
                  className: "justify-center",
                  children: [
                    /* @__PURE__ */ jsx(CloudArrowDownIcon, { className: "w-4 h-4 mr-2" }),
                    "Reporte Excel"
                  ]
                }
              ),
              /* @__PURE__ */ jsxs(
                Button,
                {
                  variant: "secondary",
                  onClick: () => exportReport("completo-pdf"),
                  className: "justify-center",
                  children: [
                    /* @__PURE__ */ jsx(CloudArrowDownIcon, { className: "w-4 h-4 mr-2" }),
                    "Reporte PDF"
                  ]
                }
              ),
              /* @__PURE__ */ jsxs(
                Button,
                {
                  variant: "secondary",
                  onClick: () => exportReport("productos-stock"),
                  className: "justify-center",
                  children: [
                    /* @__PURE__ */ jsx(CloudArrowDownIcon, { className: "w-4 h-4 mr-2" }),
                    "Stock de Productos"
                  ]
                }
              ),
              /* @__PURE__ */ jsxs(
                Button,
                {
                  variant: "secondary",
                  onClick: () => exportReport("clientes-credito"),
                  className: "justify-center",
                  children: [
                    /* @__PURE__ */ jsx(CloudArrowDownIcon, { className: "w-4 h-4 mr-2" }),
                    "Créditos Clientes"
                  ]
                }
              )
            ] }) })
          ] })
        ] }) })
      ]
    }
  );
}
export {
  Reports as default
};
