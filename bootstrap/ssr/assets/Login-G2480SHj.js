import { jsxs, Fragment, jsx } from "react/jsx-runtime";
import { useForm, Head } from "@inertiajs/react";
import { S as StatusMessage, I as Input, C as Checkbox, B as Button, A as AuthLayout, a as AuthNavigation } from "./AuthNavigation-CdKTQkGa.js";
import { useEffect } from "react";
function LoginForm({ status, canResetPassword }) {
  const { data, setData, post, processing, errors, reset } = useForm({
    email: "",
    password: "",
    remember: false
  });
  useEffect(() => {
    return () => {
      reset("password");
    };
  }, []);
  const submit = (e) => {
    e.preventDefault();
    post(route("login"));
  };
  return /* @__PURE__ */ jsxs(Fragment, { children: [
    status && /* @__PURE__ */ jsx("div", { className: "mb-6", children: /* @__PURE__ */ jsx(StatusMessage, { type: "success", message: status }) }),
    /* @__PURE__ */ jsxs("form", { onSubmit: submit, className: "space-y-6", children: [
      /* @__PURE__ */ jsx(
        Input,
        {
          id: "email",
          type: "email",
          label: "Correo Electrónico",
          value: data.email,
          onChange: (e) => setData("email", e.target.value),
          error: errors.email,
          autoComplete: "username",
          placeholder: "tu@empresa.com",
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
          autoComplete: "current-password",
          placeholder: "••••••••",
          required: true,
          icon: /* @__PURE__ */ jsx("svg", { className: "w-5 h-5", fill: "none", stroke: "currentColor", viewBox: "0 0 24 24", children: /* @__PURE__ */ jsx("path", { strokeLinecap: "round", strokeLinejoin: "round", strokeWidth: "2", d: "M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" }) })
        }
      ),
      /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
        /* @__PURE__ */ jsx(
          Checkbox,
          {
            name: "remember",
            checked: data.remember,
            onChange: (e) => setData("remember", e.target.checked),
            label: "Recordarme"
          }
        ),
        canResetPassword && /* @__PURE__ */ jsx(
          "a",
          {
            href: canResetPassword,
            className: "text-sm font-medium text-red-600 hover:text-red-500 transition-colors",
            children: "¿Olvidaste tu contraseña?"
          }
        )
      ] }),
      /* @__PURE__ */ jsx(
        Button,
        {
          type: "submit",
          size: "lg",
          isLoading: processing,
          className: "w-full",
          children: processing ? "Iniciando sesión..." : "Iniciar Sesión"
        }
      )
    ] })
  ] });
}
function DemoCredentials({ credentials }) {
  const defaultCredentials = [
    {
      email: "admin@donj.com",
      password: "password123",
      description: "Tienda Don J"
    },
    {
      email: "admin@esperanza.com",
      password: "password123",
      description: "Farmacia Esperanza"
    }
  ];
  const creds = credentials || defaultCredentials;
  return /* @__PURE__ */ jsxs("div", { className: "mt-8 p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg border border-amber-200 shadow-sm", children: [
    /* @__PURE__ */ jsxs("h4", { className: "text-sm font-semibold text-amber-800 mb-3 flex items-center", children: [
      /* @__PURE__ */ jsx("span", { className: "mr-2", children: "🔑" }),
      "Credenciales de prueba"
    ] }),
    /* @__PURE__ */ jsx("div", { className: "space-y-3", children: creds.map((cred, index) => /* @__PURE__ */ jsxs("div", { className: "bg-white/60 p-3 rounded-md border border-amber-100", children: [
      cred.description && /* @__PURE__ */ jsx("div", { className: "text-xs font-medium text-amber-700 mb-2", children: cred.description }),
      /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-2 gap-4 text-sm", children: [
        /* @__PURE__ */ jsxs("div", { className: "flex items-center", children: [
          /* @__PURE__ */ jsx("span", { className: "text-amber-700 font-medium w-16", children: "Email:" }),
          /* @__PURE__ */ jsx("span", { className: "text-amber-800 font-mono text-xs bg-amber-100 px-2 py-1 rounded flex-1 ml-2", children: cred.email })
        ] }),
        /* @__PURE__ */ jsxs("div", { className: "flex items-center", children: [
          /* @__PURE__ */ jsx("span", { className: "text-amber-700 font-medium w-20", children: "Contraseña:" }),
          /* @__PURE__ */ jsx("span", { className: "text-amber-800 font-mono text-xs bg-amber-100 px-2 py-1 rounded flex-1 ml-2", children: cred.password })
        ] })
      ] })
    ] }, index)) }),
    /* @__PURE__ */ jsx("div", { className: "mt-3 text-xs text-amber-600 text-center", children: "💡 Usa estas credenciales para explorar el sistema" })
  ] });
}
function Login({
  status,
  canResetPassword
}) {
  return /* @__PURE__ */ jsxs(Fragment, { children: [
    /* @__PURE__ */ jsx(Head, { title: "Iniciar Sesión" }),
    /* @__PURE__ */ jsxs(
      AuthLayout,
      {
        title: "¡Bienvenido de vuelta!",
        subtitle: "Inicia sesión en tu cuenta para continuar gestionando tu negocio",
        children: [
          /* @__PURE__ */ jsx(
            LoginForm,
            {
              status,
              canResetPassword
            }
          ),
          /* @__PURE__ */ jsx("div", { className: "mt-8", children: /* @__PURE__ */ jsx(AuthNavigation, { type: "login" }) }),
          /* @__PURE__ */ jsx(DemoCredentials, {})
        ]
      }
    )
  ] });
}
export {
  Login as default
};
