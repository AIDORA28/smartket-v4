import { jsxs, jsx } from "react/jsx-runtime";
import { useState } from "react";
import { Head, Link, router } from "@inertiajs/react";
import { clsx } from "clsx";
import { A as AuthenticatedLayout } from "./AuthenticatedLayout-BfjI-KKz.js";
import { C as Card, a as CardHeader, b as CardBody, B as Button } from "./Card-DoF5IT7l.js";
import { AdjustmentsHorizontalIcon, MagnifyingGlassIcon, UserIcon, PhoneIcon, EnvelopeIcon, CreditCardIcon, EyeIcon, PencilIcon, TrashIcon, PlusIcon, CloudArrowDownIcon } from "@heroicons/react/24/outline";
function Clients({ auth, clients, stats, filters }) {
  const [searchTerm, setSearchTerm] = useState(filters.search || "");
  const [selectedStatus, setSelectedStatus] = useState(filters.status || "all");
  const [selectedCredit, setSelectedCredit] = useState(filters.credit || "all");
  const filteredClients = clients.filter((client) => {
    var _a, _b, _c;
    const matchesSearch = client.nombre.toLowerCase().includes(searchTerm.toLowerCase()) || ((_a = client.email) == null ? void 0 : _a.toLowerCase().includes(searchTerm.toLowerCase())) || ((_b = client.ruc) == null ? void 0 : _b.includes(searchTerm)) || ((_c = client.telefono) == null ? void 0 : _c.includes(searchTerm));
    const matchesStatus = selectedStatus === "all" || selectedStatus === "active" && client.activo || selectedStatus === "inactive" && !client.activo;
    const matchesCredit = selectedCredit === "all" || selectedCredit === "with_credit" && client.credito_limite > 0 || selectedCredit === "no_credit" && client.credito_limite === 0 || selectedCredit === "with_debt" && client.credito_usado > 0;
    return matchesSearch && matchesStatus && matchesCredit;
  });
  const getCreditStatus = (client) => {
    if (client.credito_limite === 0) return { text: "Sin crédito", color: "gray" };
    if (client.credito_usado === 0) return { text: "Disponible", color: "green" };
    if (client.credito_usado >= client.credito_limite * 0.8) return { text: "Límite próximo", color: "red" };
    return { text: "Con deuda", color: "yellow" };
  };
  const handleDelete = (clientId) => {
    if (confirm("¿Estás seguro de eliminar este cliente?")) {
      router.delete(`/clients/${clientId}`, {
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
          /* @__PURE__ */ jsx("h2", { className: "font-semibold text-xl text-gray-800 leading-tight", children: "Gestión de Clientes" }),
          /* @__PURE__ */ jsx("p", { className: "text-gray-600 text-sm mt-1", children: "Administra tu base de clientes y sus datos" })
        ] }),
        /* @__PURE__ */ jsxs("div", { className: "flex gap-3", children: [
          /* @__PURE__ */ jsxs(Button, { variant: "secondary", size: "sm", children: [
            /* @__PURE__ */ jsx(CloudArrowDownIcon, { className: "w-4 h-4 mr-2" }),
            "Exportar"
          ] }),
          /* @__PURE__ */ jsx(Link, { href: "/clients/create", children: /* @__PURE__ */ jsxs(Button, { variant: "primary", size: "sm", children: [
            /* @__PURE__ */ jsx(PlusIcon, { className: "w-4 h-4 mr-2" }),
            "Nuevo Cliente"
          ] }) })
        ] })
      ] }),
      children: [
        /* @__PURE__ */ jsx(Head, { title: "Clientes" }),
        /* @__PURE__ */ jsx("div", { className: "py-6", children: /* @__PURE__ */ jsxs("div", { className: "max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6", children: [
          /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-2 md:grid-cols-4 gap-4", children: [
            /* @__PURE__ */ jsxs("div", { className: "bg-blue-50 p-4 rounded-lg border", children: [
              /* @__PURE__ */ jsx("div", { className: "text-2xl font-bold text-blue-600", children: stats.total_clientes.toLocaleString() }),
              /* @__PURE__ */ jsx("div", { className: "text-sm text-blue-700", children: "Total Clientes" })
            ] }),
            /* @__PURE__ */ jsxs("div", { className: "bg-green-50 p-4 rounded-lg border", children: [
              /* @__PURE__ */ jsx("div", { className: "text-2xl font-bold text-green-600", children: stats.clientes_activos.toLocaleString() }),
              /* @__PURE__ */ jsx("div", { className: "text-sm text-green-700", children: "Activos" })
            ] }),
            /* @__PURE__ */ jsxs("div", { className: "bg-purple-50 p-4 rounded-lg border", children: [
              /* @__PURE__ */ jsx("div", { className: "text-2xl font-bold text-purple-600", children: stats.con_credito.toLocaleString() }),
              /* @__PURE__ */ jsx("div", { className: "text-sm text-purple-700", children: "Con Crédito" })
            ] }),
            /* @__PURE__ */ jsxs("div", { className: "bg-orange-50 p-4 rounded-lg border", children: [
              /* @__PURE__ */ jsxs("div", { className: "text-2xl font-bold text-orange-600", children: [
                "$",
                stats.credito_pendiente.toLocaleString()
              ] }),
              /* @__PURE__ */ jsx("div", { className: "text-sm text-orange-700", children: "Crédito Pendiente" })
            ] })
          ] }),
          /* @__PURE__ */ jsxs(Card, { children: [
            /* @__PURE__ */ jsx(CardHeader, { children: /* @__PURE__ */ jsxs("div", { className: "flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4", children: [
              /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900", children: "Filtros y Búsqueda" }),
              /* @__PURE__ */ jsxs("div", { className: "flex items-center gap-2", children: [
                /* @__PURE__ */ jsx(AdjustmentsHorizontalIcon, { className: "w-5 h-5 text-gray-500" }),
                /* @__PURE__ */ jsxs("span", { className: "text-sm text-gray-500", children: [
                  filteredClients.length,
                  " de ",
                  clients.length,
                  " clientes"
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
                    placeholder: "Buscar clientes...",
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
                  value: selectedStatus,
                  onChange: (e) => setSelectedStatus(e.target.value),
                  children: [
                    /* @__PURE__ */ jsx("option", { value: "all", children: "Todos los estados" }),
                    /* @__PURE__ */ jsx("option", { value: "active", children: "Activos" }),
                    /* @__PURE__ */ jsx("option", { value: "inactive", children: "Inactivos" })
                  ]
                }
              ),
              /* @__PURE__ */ jsxs(
                "select",
                {
                  className: "px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500",
                  value: selectedCredit,
                  onChange: (e) => setSelectedCredit(e.target.value),
                  children: [
                    /* @__PURE__ */ jsx("option", { value: "all", children: "Todos los créditos" }),
                    /* @__PURE__ */ jsx("option", { value: "with_credit", children: "Con crédito" }),
                    /* @__PURE__ */ jsx("option", { value: "no_credit", children: "Sin crédito" }),
                    /* @__PURE__ */ jsx("option", { value: "with_debt", children: "Con deuda" })
                  ]
                }
              )
            ] }) })
          ] }),
          /* @__PURE__ */ jsxs(Card, { children: [
            /* @__PURE__ */ jsx(CardHeader, { children: /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900", children: "Lista de Clientes" }) }),
            /* @__PURE__ */ jsx(CardBody, { children: /* @__PURE__ */ jsxs("div", { className: "overflow-x-auto", children: [
              /* @__PURE__ */ jsxs("table", { className: "min-w-full divide-y divide-gray-200", children: [
                /* @__PURE__ */ jsx("thead", { className: "bg-gray-50", children: /* @__PURE__ */ jsxs("tr", { children: [
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Cliente" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Contacto" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Crédito" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Compras" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Estado" }),
                  /* @__PURE__ */ jsx("th", { className: "px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider", children: "Acciones" })
                ] }) }),
                /* @__PURE__ */ jsx("tbody", { className: "bg-white divide-y divide-gray-200", children: filteredClients.map((client) => {
                  const creditStatus = getCreditStatus(client);
                  return /* @__PURE__ */ jsxs("tr", { className: "hover:bg-gray-50", children: [
                    /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center", children: [
                      /* @__PURE__ */ jsx("div", { className: "flex-shrink-0 h-10 w-10", children: /* @__PURE__ */ jsx("div", { className: "h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center", children: /* @__PURE__ */ jsx(UserIcon, { className: "h-6 w-6 text-gray-600" }) }) }),
                      /* @__PURE__ */ jsxs("div", { className: "ml-4", children: [
                        /* @__PURE__ */ jsx("div", { className: "text-sm font-medium text-gray-900", children: client.nombre }),
                        client.ruc && /* @__PURE__ */ jsxs("div", { className: "text-sm text-gray-500", children: [
                          "RUC: ",
                          client.ruc
                        ] })
                      ] })
                    ] }) }),
                    /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsxs("div", { className: "text-sm text-gray-900", children: [
                      client.telefono && /* @__PURE__ */ jsxs("div", { className: "flex items-center mb-1", children: [
                        /* @__PURE__ */ jsx(PhoneIcon, { className: "w-4 h-4 mr-2 text-gray-400" }),
                        client.telefono
                      ] }),
                      client.email && /* @__PURE__ */ jsxs("div", { className: "flex items-center", children: [
                        /* @__PURE__ */ jsx(EnvelopeIcon, { className: "w-4 h-4 mr-2 text-gray-400" }),
                        /* @__PURE__ */ jsx("span", { className: "text-xs", children: client.email })
                      ] })
                    ] }) }),
                    /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsxs("div", { className: "text-sm text-gray-900", children: [
                      /* @__PURE__ */ jsxs("div", { className: "flex items-center mb-1", children: [
                        /* @__PURE__ */ jsx(CreditCardIcon, { className: "w-4 h-4 mr-2 text-gray-400" }),
                        "$",
                        client.credito_limite.toLocaleString()
                      ] }),
                      client.credito_usado > 0 && /* @__PURE__ */ jsxs("div", { className: "text-xs text-red-600", children: [
                        "Usado: $",
                        client.credito_usado.toLocaleString()
                      ] }),
                      /* @__PURE__ */ jsx("span", { className: clsx(
                        "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium",
                        creditStatus.color === "green" && "bg-green-100 text-green-800",
                        creditStatus.color === "yellow" && "bg-yellow-100 text-yellow-800",
                        creditStatus.color === "red" && "bg-red-100 text-red-800",
                        creditStatus.color === "gray" && "bg-gray-100 text-gray-800"
                      ), children: creditStatus.text })
                    ] }) }),
                    /* @__PURE__ */ jsxs("td", { className: "px-6 py-4 whitespace-nowrap text-sm text-gray-900", children: [
                      /* @__PURE__ */ jsxs("div", { children: [
                        "Total: $",
                        client.total_compras.toLocaleString()
                      ] }),
                      client.ultima_compra && /* @__PURE__ */ jsxs("div", { className: "text-xs text-gray-500", children: [
                        "Última: ",
                        new Date(client.ultima_compra).toLocaleDateString()
                      ] })
                    ] }),
                    /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap", children: /* @__PURE__ */ jsx("span", { className: clsx(
                      "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium",
                      client.activo ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800"
                    ), children: client.activo ? "Activo" : "Inactivo" }) }),
                    /* @__PURE__ */ jsx("td", { className: "px-6 py-4 whitespace-nowrap text-right text-sm font-medium", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-end gap-2", children: [
                      /* @__PURE__ */ jsx(Link, { href: `/clients/${client.id}`, children: /* @__PURE__ */ jsx(Button, { variant: "ghost", size: "sm", children: /* @__PURE__ */ jsx(EyeIcon, { className: "w-4 h-4" }) }) }),
                      /* @__PURE__ */ jsx(Link, { href: `/clients/${client.id}/edit`, children: /* @__PURE__ */ jsx(Button, { variant: "ghost", size: "sm", children: /* @__PURE__ */ jsx(PencilIcon, { className: "w-4 h-4" }) }) }),
                      /* @__PURE__ */ jsx(
                        Button,
                        {
                          variant: "ghost",
                          size: "sm",
                          className: "text-red-600 hover:text-red-700",
                          onClick: () => handleDelete(client.id),
                          children: /* @__PURE__ */ jsx(TrashIcon, { className: "w-4 h-4" })
                        }
                      )
                    ] }) })
                  ] }, client.id);
                }) })
              ] }),
              filteredClients.length === 0 && /* @__PURE__ */ jsxs("div", { className: "text-center py-12", children: [
                /* @__PURE__ */ jsx("div", { className: "text-gray-400 text-6xl mb-4", children: "👥" }),
                /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900 mb-2", children: "No se encontraron clientes" }),
                /* @__PURE__ */ jsx("p", { className: "text-gray-500 mb-6", children: searchTerm || selectedStatus !== "all" || selectedCredit !== "all" ? "Intenta ajustar los filtros de búsqueda" : "Comienza agregando tu primer cliente" }),
                !searchTerm && selectedStatus === "all" && selectedCredit === "all" && /* @__PURE__ */ jsx(Link, { href: "/clients/create", children: /* @__PURE__ */ jsxs(Button, { variant: "primary", children: [
                  /* @__PURE__ */ jsx(PlusIcon, { className: "w-4 h-4 mr-2" }),
                  "Crear Primer Cliente"
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
  Clients as default
};
