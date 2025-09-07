import { jsxs, Fragment, jsx } from "react/jsx-runtime";
import { useForm, Head } from "@inertiajs/react";
import { S as StatusMessage, B as Button, C as Checkbox, I as Input, A as AuthLayout, a as AuthNavigation } from "./AuthNavigation-CdKTQkGa.js";
import { useState, useEffect } from "react";
function RegisterForm({ selectedPlanParam = "estandar", planes = [], rubros = [] }) {
  var _a, _b, _c;
  const [currentStep, setCurrentStep] = useState(1);
  const [selectedPlanId, setSelectedPlanId] = useState(
    ((_a = planes.find((p) => p.id.toString() === selectedPlanParam || p.nombre.toLowerCase().includes(selectedPlanParam))) == null ? void 0 : _a.id) || ((_b = planes[1]) == null ? void 0 : _b.id) || 1
  );
  const [selectedRubroId, setSelectedRubroId] = useState(((_c = rubros[0]) == null ? void 0 : _c.id) || 1);
  const { data, setData, post, processing, errors, reset } = useForm({
    // Paso 1: Datos personales
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    // Paso 2: Datos de la empresa
    empresa_nombre: "",
    empresa_ruc: "",
    tiene_ruc: false,
    // Paso 3: Plan y rubro
    plan_id: selectedPlanId,
    rubro_id: selectedRubroId,
    // Términos
    terms: false
  });
  useEffect(() => {
    return () => {
      reset("password", "password_confirmation");
    };
  }, []);
  useEffect(() => {
    setData((prevData) => ({
      ...prevData,
      plan_id: selectedPlanId,
      rubro_id: selectedRubroId
    }));
  }, [selectedPlanId, selectedRubroId]);
  const validateStep = (step) => {
    switch (step) {
      case 1:
        return !!(data.name && data.email && data.password && data.password_confirmation);
      case 2:
        return !!(data.empresa_nombre && (!data.tiene_ruc || data.empresa_ruc));
      case 3:
        return !!(data.terms && data.plan_id && data.rubro_id);
      default:
        return true;
    }
  };
  const submit = (e) => {
    e.preventDefault();
    if (currentStep < 3) {
      if (validateStep(currentStep)) {
        setCurrentStep(currentStep + 1);
      }
      return;
    }
    post(route("register"));
  };
  const goBack = () => {
    if (currentStep > 1) {
      setCurrentStep(currentStep - 1);
    }
  };
  const currentPlan = planes.find((p) => p.id === selectedPlanId);
  const currentRubro = rubros.find((r) => r.id === selectedRubroId);
  const renderStepContent = () => {
    switch (currentStep) {
      case 1:
        return /* @__PURE__ */ jsxs("div", { className: "space-y-6", children: [
          /* @__PURE__ */ jsxs("div", { className: "text-center mb-8", children: [
            /* @__PURE__ */ jsx("h3", { className: "text-xl font-semibold text-gray-900 mb-2", children: "Datos Personales" }),
            /* @__PURE__ */ jsx("p", { className: "text-gray-600", children: "Información del administrador principal" })
          ] }),
          /* @__PURE__ */ jsx(
            Input,
            {
              id: "name",
              type: "text",
              label: "Nombre Completo",
              value: data.name,
              onChange: (e) => setData("name", e.target.value),
              error: errors.name,
              placeholder: "Juan Pérez",
              required: true,
              icon: /* @__PURE__ */ jsx("svg", { className: "w-5 h-5", fill: "none", stroke: "currentColor", viewBox: "0 0 24 24", children: /* @__PURE__ */ jsx("path", { strokeLinecap: "round", strokeLinejoin: "round", strokeWidth: "2", d: "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" }) })
            }
          ),
          /* @__PURE__ */ jsx(
            Input,
            {
              id: "email",
              type: "email",
              label: "Correo Electrónico",
              value: data.email,
              onChange: (e) => setData("email", e.target.value),
              error: errors.email,
              placeholder: "juan@empresa.com",
              required: true,
              icon: /* @__PURE__ */ jsx("svg", { className: "w-5 h-5", fill: "none", stroke: "currentColor", viewBox: "0 0 24 24", children: /* @__PURE__ */ jsx("path", { strokeLinecap: "round", strokeLinejoin: "round", strokeWidth: "2", d: "M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" }) })
            }
          ),
          /* @__PURE__ */ jsx(
            Input,
            {
              id: "password",
              type: "password",
              label: "Contraseña",
              value: data.password,
              onChange: (e) => setData("password", e.target.value),
              error: errors.password,
              placeholder: "••••••••",
              required: true,
              icon: /* @__PURE__ */ jsx("svg", { className: "w-5 h-5", fill: "none", stroke: "currentColor", viewBox: "0 0 24 24", children: /* @__PURE__ */ jsx("path", { strokeLinecap: "round", strokeLinejoin: "round", strokeWidth: "2", d: "M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" }) })
            }
          ),
          /* @__PURE__ */ jsx(
            Input,
            {
              id: "password_confirmation",
              type: "password",
              label: "Confirmar Contraseña",
              value: data.password_confirmation,
              onChange: (e) => setData("password_confirmation", e.target.value),
              error: errors.password_confirmation,
              placeholder: "••••••••",
              required: true,
              icon: /* @__PURE__ */ jsx("svg", { className: "w-5 h-5", fill: "none", stroke: "currentColor", viewBox: "0 0 24 24", children: /* @__PURE__ */ jsx("path", { strokeLinecap: "round", strokeLinejoin: "round", strokeWidth: "2", d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" }) })
            }
          )
        ] });
      case 2:
        return /* @__PURE__ */ jsxs("div", { className: "space-y-6", children: [
          /* @__PURE__ */ jsxs("div", { className: "text-center mb-8", children: [
            /* @__PURE__ */ jsx("h3", { className: "text-xl font-semibold text-gray-900 mb-2", children: "Datos de la Empresa" }),
            /* @__PURE__ */ jsx("p", { className: "text-gray-600", children: "Información de tu negocio" })
          ] }),
          /* @__PURE__ */ jsx(
            Input,
            {
              id: "empresa_nombre",
              type: "text",
              label: "Nombre de la Empresa",
              value: data.empresa_nombre,
              onChange: (e) => setData("empresa_nombre", e.target.value),
              error: errors.empresa_nombre,
              placeholder: "Panadería San Miguel",
              required: true,
              icon: /* @__PURE__ */ jsx("svg", { className: "w-5 h-5", fill: "none", stroke: "currentColor", viewBox: "0 0 24 24", children: /* @__PURE__ */ jsx("path", { strokeLinecap: "round", strokeLinejoin: "round", strokeWidth: "2", d: "M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" }) })
            }
          ),
          /* @__PURE__ */ jsxs("div", { className: "space-y-4", children: [
            /* @__PURE__ */ jsx(
              Checkbox,
              {
                checked: data.tiene_ruc,
                onChange: (e) => setData("tiene_ruc", e.target.checked),
                label: "Mi empresa tiene RUC"
              }
            ),
            data.tiene_ruc && /* @__PURE__ */ jsx(
              Input,
              {
                id: "empresa_ruc",
                type: "text",
                label: "RUC",
                value: data.empresa_ruc,
                onChange: (e) => setData("empresa_ruc", e.target.value),
                error: errors.empresa_ruc,
                placeholder: "20123456789",
                maxLength: 11,
                icon: /* @__PURE__ */ jsx("svg", { className: "w-5 h-5", fill: "none", stroke: "currentColor", viewBox: "0 0 24 24", children: /* @__PURE__ */ jsx("path", { strokeLinecap: "round", strokeLinejoin: "round", strokeWidth: "2", d: "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" }) })
              }
            )
          ] })
        ] });
      case 3:
        return /* @__PURE__ */ jsxs("div", { className: "space-y-8", children: [
          /* @__PURE__ */ jsxs("div", { className: "text-center mb-8", children: [
            /* @__PURE__ */ jsx("h3", { className: "text-xl font-semibold text-gray-900 mb-2", children: "Plan y Tipo de Negocio" }),
            /* @__PURE__ */ jsx("p", { className: "text-gray-600", children: "Selecciona tu plan y rubro empresarial" })
          ] }),
          /* @__PURE__ */ jsxs("div", { children: [
            /* @__PURE__ */ jsx("h4", { className: "text-lg font-medium text-gray-900 mb-4", children: "Selecciona tu Plan" }),
            /* @__PURE__ */ jsx("div", { className: "grid grid-cols-1 md:grid-cols-3 gap-4", children: planes.map((plan) => /* @__PURE__ */ jsx(
              "div",
              {
                className: `p-4 border rounded-lg cursor-pointer transition-all ${selectedPlanId === plan.id ? "border-red-500 bg-red-50 ring-2 ring-red-200" : "border-gray-200 hover:border-gray-300 hover:shadow-md"}`,
                onClick: () => setSelectedPlanId(plan.id),
                children: /* @__PURE__ */ jsxs("div", { className: "text-center", children: [
                  /* @__PURE__ */ jsx("h5", { className: "font-semibold text-gray-900 mb-2", children: plan.nombre }),
                  /* @__PURE__ */ jsx("div", { className: "mb-3", children: plan.es_gratis ? /* @__PURE__ */ jsx("div", { className: "text-2xl font-bold text-green-600", children: "GRATIS" }) : /* @__PURE__ */ jsxs("div", { children: [
                    /* @__PURE__ */ jsxs("div", { className: "text-2xl font-bold text-gray-900", children: [
                      "S/ ",
                      plan.precio_mensual
                    ] }),
                    /* @__PURE__ */ jsx("div", { className: "text-sm text-gray-500", children: "/mes" }),
                    plan.dias_prueba > 0 && /* @__PURE__ */ jsxs("div", { className: "text-xs text-blue-600 font-medium", children: [
                      plan.dias_prueba,
                      " días gratis"
                    ] })
                  ] }) }),
                  /* @__PURE__ */ jsxs("div", { className: "text-sm text-gray-600 space-y-1", children: [
                    /* @__PURE__ */ jsxs("div", { children: [
                      "👥 ",
                      plan.max_usuarios,
                      " usuarios"
                    ] }),
                    /* @__PURE__ */ jsxs("div", { children: [
                      "🏪 ",
                      plan.max_sucursales,
                      " sucursal",
                      plan.max_sucursales > 1 ? "es" : ""
                    ] }),
                    /* @__PURE__ */ jsxs("div", { children: [
                      "🏷️ ",
                      plan.max_rubros,
                      " rubro",
                      plan.max_rubros > 1 ? "s" : ""
                    ] }),
                    /* @__PURE__ */ jsxs("div", { children: [
                      "📦 ",
                      plan.max_productos.toLocaleString(),
                      " productos"
                    ] })
                  ] }),
                  /* @__PURE__ */ jsx("p", { className: "text-xs text-gray-500 mt-2", children: plan.descripcion })
                ] })
              },
              plan.id
            )) })
          ] }),
          /* @__PURE__ */ jsxs("div", { children: [
            /* @__PURE__ */ jsx("h4", { className: "text-lg font-medium text-gray-900 mb-4", children: "Tipo de Negocio" }),
            /* @__PURE__ */ jsx("div", { className: "grid grid-cols-1 md:grid-cols-2 gap-4", children: rubros.map((rubro) => /* @__PURE__ */ jsx(
              "div",
              {
                className: `p-4 border rounded-lg cursor-pointer transition-all ${selectedRubroId === rubro.id ? "border-red-500 bg-red-50 ring-2 ring-red-200" : "border-gray-200 hover:border-gray-300 hover:shadow-md"}`,
                onClick: () => setSelectedRubroId(rubro.id),
                children: /* @__PURE__ */ jsxs("div", { className: "flex items-start", children: [
                  /* @__PURE__ */ jsx(
                    "input",
                    {
                      type: "radio",
                      name: "rubro",
                      value: rubro.id,
                      checked: selectedRubroId === rubro.id,
                      onChange: () => setSelectedRubroId(rubro.id),
                      className: "mt-1 text-red-600 focus:ring-red-500"
                    }
                  ),
                  /* @__PURE__ */ jsx("div", { className: "ml-3", children: /* @__PURE__ */ jsx("h5", { className: "font-semibold text-gray-900", children: rubro.nombre }) })
                ] })
              },
              rubro.id
            )) })
          ] }),
          /* @__PURE__ */ jsxs("div", { className: "border-t pt-6", children: [
            /* @__PURE__ */ jsx(
              Checkbox,
              {
                checked: data.terms,
                onChange: (e) => setData("terms", e.target.checked),
                label: "Acepto los términos y condiciones y la política de privacidad"
              }
            ),
            errors.terms && /* @__PURE__ */ jsx("p", { className: "mt-1 text-sm text-red-600", children: errors.terms })
          ] }),
          /* @__PURE__ */ jsxs("div", { className: "bg-gray-50 p-4 rounded-lg", children: [
            /* @__PURE__ */ jsx("h5", { className: "font-medium text-gray-900 mb-2", children: "Resumen de tu registro:" }),
            /* @__PURE__ */ jsxs("div", { className: "text-sm text-gray-600 space-y-1", children: [
              /* @__PURE__ */ jsxs("div", { children: [
                "📧 ",
                /* @__PURE__ */ jsx("strong", { children: "Email:" }),
                " ",
                data.email
              ] }),
              /* @__PURE__ */ jsxs("div", { children: [
                "🏢 ",
                /* @__PURE__ */ jsx("strong", { children: "Empresa:" }),
                " ",
                data.empresa_nombre
              ] }),
              /* @__PURE__ */ jsxs("div", { children: [
                "📋 ",
                /* @__PURE__ */ jsx("strong", { children: "Plan:" }),
                " ",
                currentPlan == null ? void 0 : currentPlan.nombre
              ] }),
              /* @__PURE__ */ jsxs("div", { children: [
                "🏷️ ",
                /* @__PURE__ */ jsx("strong", { children: "Rubro:" }),
                " ",
                currentRubro == null ? void 0 : currentRubro.nombre
              ] })
            ] })
          ] })
        ] });
      default:
        return null;
    }
  };
  return /* @__PURE__ */ jsxs(Fragment, { children: [
    /* @__PURE__ */ jsxs("div", { className: "mb-8", children: [
      /* @__PURE__ */ jsx("div", { className: "flex items-center justify-between mb-4", children: [1, 2, 3].map((step) => /* @__PURE__ */ jsxs("div", { className: "flex items-center flex-1", children: [
        /* @__PURE__ */ jsx(
          "div",
          {
            className: `w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold transition-colors ${step <= currentStep ? "bg-red-600 text-white" : "bg-gray-200 text-gray-500"}`,
            children: step
          }
        ),
        step < 3 && /* @__PURE__ */ jsx(
          "div",
          {
            className: `flex-1 h-1 mx-4 rounded transition-colors ${step < currentStep ? "bg-red-600" : "bg-gray-200"}`
          }
        )
      ] }, step)) }),
      /* @__PURE__ */ jsxs("div", { className: "text-center text-sm text-gray-600", children: [
        "Paso ",
        currentStep,
        " de 3"
      ] })
    ] }),
    Object.keys(errors).length > 0 && !errors.name && !errors.email && !errors.password && !errors.password_confirmation && /* @__PURE__ */ jsx("div", { className: "mb-6", children: /* @__PURE__ */ jsx(StatusMessage, { type: "error", message: "Por favor corrige los errores en el formulario" }) }),
    /* @__PURE__ */ jsxs("form", { onSubmit: submit, className: "space-y-6", children: [
      renderStepContent(),
      /* @__PURE__ */ jsxs("div", { className: "flex justify-between pt-6", children: [
        currentStep > 1 && /* @__PURE__ */ jsxs(
          Button,
          {
            type: "button",
            variant: "outline",
            onClick: goBack,
            className: "flex items-center",
            children: [
              /* @__PURE__ */ jsx("svg", { className: "w-4 h-4 mr-2", fill: "none", stroke: "currentColor", viewBox: "0 0 24 24", children: /* @__PURE__ */ jsx("path", { strokeLinecap: "round", strokeLinejoin: "round", strokeWidth: "2", d: "M10 19l-7-7m0 0l7-7m-7 7h18" }) }),
              "Anterior"
            ]
          }
        ),
        /* @__PURE__ */ jsx(
          Button,
          {
            type: "submit",
            size: "lg",
            isLoading: processing,
            disabled: !validateStep(currentStep),
            className: `${currentStep === 1 ? "w-full" : "ml-auto"} flex items-center`,
            children: currentStep < 3 ? /* @__PURE__ */ jsxs(Fragment, { children: [
              "Siguiente",
              /* @__PURE__ */ jsx("svg", { className: "w-4 h-4 ml-2", fill: "none", stroke: "currentColor", viewBox: "0 0 24 24", children: /* @__PURE__ */ jsx("path", { strokeLinecap: "round", strokeLinejoin: "round", strokeWidth: "2", d: "M14 5l7 7m0 0l-7 7m7-7H3" }) })
            ] }) : "🚀 Crear mi cuenta"
          }
        )
      ] })
    ] })
  ] });
}
function Register({ selectedPlanParam = "estandar", planes = [], rubros = [] }) {
  return /* @__PURE__ */ jsxs(Fragment, { children: [
    /* @__PURE__ */ jsx(Head, { title: "Registro - SmartKet" }),
    /* @__PURE__ */ jsxs(
      AuthLayout,
      {
        title: "Crea tu cuenta",
        subtitle: "Únete a miles de empresas que ya confían en SmartKet",
        children: [
          /* @__PURE__ */ jsx(
            RegisterForm,
            {
              selectedPlanParam,
              planes,
              rubros
            }
          ),
          /* @__PURE__ */ jsx("div", { className: "mt-8", children: /* @__PURE__ */ jsx(AuthNavigation, { type: "register" }) })
        ]
      }
    )
  ] });
}
export {
  Register as default
};
