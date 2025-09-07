import { jsxs, jsx } from "react/jsx-runtime";
import { Head } from "@inertiajs/react";
import { A as AuthenticatedLayout } from "./AuthenticatedLayout-BfjI-KKz.js";
import { CheckCircleIcon, ClockIcon, WrenchScrewdriverIcon } from "@heroicons/react/24/outline";
import "react";
import "clsx";
function Placeholder({
  module,
  description = "Este módulo está en desarrollo",
  status = "development"
}) {
  const statusConfig = {
    development: {
      icon: WrenchScrewdriverIcon,
      color: "text-yellow-600",
      bgColor: "bg-yellow-50",
      borderColor: "border-yellow-200",
      title: "En Desarrollo",
      message: "Estamos trabajando en este módulo."
    },
    "coming-soon": {
      icon: ClockIcon,
      color: "text-blue-600",
      bgColor: "bg-blue-50",
      borderColor: "border-blue-200",
      title: "Próximamente",
      message: "Este módulo estará disponible pronto."
    },
    completed: {
      icon: CheckCircleIcon,
      color: "text-green-600",
      bgColor: "bg-green-50",
      borderColor: "border-green-200",
      title: "Completado",
      message: "Este módulo está completamente funcional."
    }
  };
  const config = statusConfig[status];
  const Icon = config.icon;
  return /* @__PURE__ */ jsxs(AuthenticatedLayout, { title: module, children: [
    /* @__PURE__ */ jsx(Head, { title: module }),
    /* @__PURE__ */ jsx("div", { className: "max-w-4xl mx-auto", children: /* @__PURE__ */ jsxs("div", { className: `rounded-lg p-8 text-center ${config.bgColor} ${config.borderColor} border-2 border-dashed`, children: [
      /* @__PURE__ */ jsx(Icon, { className: `mx-auto h-16 w-16 ${config.color} mb-4` }),
      /* @__PURE__ */ jsx("h1", { className: `text-2xl font-bold ${config.color} mb-2`, children: module }),
      /* @__PURE__ */ jsx("div", { className: "mb-4", children: /* @__PURE__ */ jsx("span", { className: `inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${config.bgColor} ${config.color} border ${config.borderColor}`, children: config.title }) }),
      /* @__PURE__ */ jsx("p", { className: "text-gray-600 mb-6", children: description }),
      /* @__PURE__ */ jsx("p", { className: `text-sm ${config.color} font-medium`, children: config.message }),
      /* @__PURE__ */ jsxs("div", { className: "mt-8 bg-white rounded-lg p-4 border border-gray-200", children: [
        /* @__PURE__ */ jsx("h3", { className: "text-sm font-medium text-gray-900 mb-3", children: "Funcionalidades Planificadas:" }),
        /* @__PURE__ */ jsxs("div", { className: "text-left space-y-2", children: [
          /* @__PURE__ */ jsxs("div", { className: "flex items-center text-sm text-gray-600", children: [
            /* @__PURE__ */ jsx("div", { className: "w-2 h-2 bg-gray-300 rounded-full mr-3" }),
            "Interfaz moderna con React + TypeScript"
          ] }),
          /* @__PURE__ */ jsxs("div", { className: "flex items-center text-sm text-gray-600", children: [
            /* @__PURE__ */ jsx("div", { className: "w-2 h-2 bg-gray-300 rounded-full mr-3" }),
            "Integración completa con Inertia.js"
          ] }),
          /* @__PURE__ */ jsxs("div", { className: "flex items-center text-sm text-gray-600", children: [
            /* @__PURE__ */ jsx("div", { className: "w-2 h-2 bg-gray-300 rounded-full mr-3" }),
            "Validaciones y feedback en tiempo real"
          ] }),
          /* @__PURE__ */ jsxs("div", { className: "flex items-center text-sm text-gray-600", children: [
            /* @__PURE__ */ jsx("div", { className: "w-2 h-2 bg-gray-300 rounded-full mr-3" }),
            "Responsive y optimizado para móviles"
          ] })
        ] })
      ] })
    ] }) })
  ] });
}
export {
  Placeholder as default
};
