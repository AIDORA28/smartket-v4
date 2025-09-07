import { jsxs, jsx } from "react/jsx-runtime";
import { Link } from "@inertiajs/react";
function AuthLayout({ children, title, subtitle }) {
  return /* @__PURE__ */ jsxs("div", { className: "min-h-screen flex", children: [
    /* @__PURE__ */ jsxs("div", { className: "hidden lg:flex lg:w-1/2 bg-gradient-to-br from-red-600 to-red-800 relative overflow-hidden", children: [
      /* @__PURE__ */ jsx("div", { className: "absolute inset-0 bg-black/20" }),
      /* @__PURE__ */ jsxs("div", { className: "relative z-10 flex flex-col justify-center px-12 text-white", children: [
        /* @__PURE__ */ jsx("div", { className: "mb-8", children: /* @__PURE__ */ jsxs(Link, { href: "/", className: "inline-flex items-center", children: [
          /* @__PURE__ */ jsx(
            "img",
            {
              src: "/img/SmartKet.svg",
              alt: "SmartKet",
              className: "h-12 w-auto filter brightness-0 invert"
            }
          ),
          /* @__PURE__ */ jsxs("span", { className: "ml-4 text-3xl font-bold", children: [
            /* @__PURE__ */ jsx("span", { className: "text-amber-300 uppercase tracking-wide", children: "SMART" }),
            /* @__PURE__ */ jsx("span", { className: "lowercase", children: "ket" })
          ] })
        ] }) }),
        /* @__PURE__ */ jsxs("div", { className: "max-w-md", children: [
          /* @__PURE__ */ jsx("h1", { className: "text-4xl font-bold mb-4", children: "Gestiona tu negocio de manera inteligente" }),
          /* @__PURE__ */ jsx("p", { className: "text-xl text-red-100 leading-relaxed", children: "Control total de inventario, ventas y clientes en una sola plataforma." })
        ] }),
        /* @__PURE__ */ jsxs("div", { className: "mt-12 space-y-4", children: [
          /* @__PURE__ */ jsxs("div", { className: "flex items-center text-red-100", children: [
            /* @__PURE__ */ jsx("div", { className: "w-2 h-2 bg-amber-300 rounded-full mr-3" }),
            /* @__PURE__ */ jsx("span", { children: "Control de inventario en tiempo real" })
          ] }),
          /* @__PURE__ */ jsxs("div", { className: "flex items-center text-red-100", children: [
            /* @__PURE__ */ jsx("div", { className: "w-2 h-2 bg-amber-300 rounded-full mr-3" }),
            /* @__PURE__ */ jsx("span", { children: "Reportes detallados y analytics" })
          ] }),
          /* @__PURE__ */ jsxs("div", { className: "flex items-center text-red-100", children: [
            /* @__PURE__ */ jsx("div", { className: "w-2 h-2 bg-amber-300 rounded-full mr-3" }),
            /* @__PURE__ */ jsx("span", { children: "Soporte 24/7 especializado" })
          ] })
        ] })
      ] }),
      /* @__PURE__ */ jsx("div", { className: "absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-20 translate-x-20" }),
      /* @__PURE__ */ jsx("div", { className: "absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full translate-y-16 -translate-x-16" })
    ] }),
    /* @__PURE__ */ jsx("div", { className: "flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24 bg-gray-50", children: /* @__PURE__ */ jsxs("div", { className: "mx-auto w-full max-w-sm lg:max-w-md", children: [
      /* @__PURE__ */ jsx("div", { className: "lg:hidden text-center mb-8", children: /* @__PURE__ */ jsxs(Link, { href: "/", className: "inline-flex items-center", children: [
        /* @__PURE__ */ jsx(
          "img",
          {
            src: "/img/SmartKet.svg",
            alt: "SmartKet",
            className: "h-10 w-auto"
          }
        ),
        /* @__PURE__ */ jsxs("span", { className: "ml-3 text-2xl font-bold", children: [
          /* @__PURE__ */ jsx("span", { className: "text-amber-500 uppercase tracking-wide", children: "SMART" }),
          /* @__PURE__ */ jsx("span", { className: "text-gray-900 lowercase", children: "ket" })
        ] })
      ] }) }),
      /* @__PURE__ */ jsxs("div", { className: "text-center lg:text-left", children: [
        /* @__PURE__ */ jsx("h2", { className: "text-3xl font-bold text-gray-900 mb-2", children: title }),
        subtitle && /* @__PURE__ */ jsx("p", { className: "text-gray-600 mb-8", children: subtitle })
      ] }),
      children
    ] }) })
  ] });
}
function Input({
  label,
  error,
  icon,
  className = "",
  ...props
}) {
  return /* @__PURE__ */ jsxs("div", { className: "space-y-1", children: [
    /* @__PURE__ */ jsx(
      "label",
      {
        htmlFor: props.id,
        className: "block text-sm font-semibold text-gray-700",
        children: label
      }
    ),
    /* @__PURE__ */ jsxs("div", { className: "relative", children: [
      icon && /* @__PURE__ */ jsx("div", { className: "absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400", children: icon }),
      /* @__PURE__ */ jsx(
        "input",
        {
          ...props,
          className: `
            w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm
            focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500
            transition-all duration-300
            ${icon ? "pl-10" : ""}
            ${error ? "border-red-300 focus:ring-red-500" : "border-gray-300"}
            ${className}
          `
        }
      )
    ] }),
    error && /* @__PURE__ */ jsx("p", { className: "text-sm text-red-600 mt-1", children: error })
  ] });
}
function Button({
  variant = "primary",
  size = "md",
  isLoading = false,
  children,
  className = "",
  disabled,
  ...props
}) {
  const baseClasses = "inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed";
  const variants = {
    primary: "bg-red-600 hover:bg-red-700 text-white focus:ring-red-500 shadow-lg hover:shadow-xl",
    secondary: "bg-gray-200 hover:bg-gray-300 text-gray-900 focus:ring-gray-500",
    outline: "border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white focus:ring-red-500"
  };
  const sizes = {
    sm: "px-3 py-2 text-sm",
    md: "px-6 py-3 text-base",
    lg: "px-8 py-4 text-lg"
  };
  return /* @__PURE__ */ jsxs(
    "button",
    {
      ...props,
      disabled: disabled || isLoading,
      className: `
        ${baseClasses}
        ${variants[variant]}
        ${sizes[size]}
        ${className}
      `,
      children: [
        isLoading && /* @__PURE__ */ jsxs(
          "svg",
          {
            className: "animate-spin -ml-1 mr-3 h-5 w-5 text-current",
            xmlns: "http://www.w3.org/2000/svg",
            fill: "none",
            viewBox: "0 0 24 24",
            children: [
              /* @__PURE__ */ jsx(
                "circle",
                {
                  className: "opacity-25",
                  cx: "12",
                  cy: "12",
                  r: "10",
                  stroke: "currentColor",
                  strokeWidth: "4"
                }
              ),
              /* @__PURE__ */ jsx(
                "path",
                {
                  className: "opacity-75",
                  fill: "currentColor",
                  d: "M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                }
              )
            ]
          }
        ),
        children
      ]
    }
  );
}
function Checkbox({ label, className = "", ...props }) {
  return /* @__PURE__ */ jsxs("label", { className: "flex items-center cursor-pointer group", children: [
    /* @__PURE__ */ jsx("div", { className: "relative", children: /* @__PURE__ */ jsx(
      "input",
      {
        type: "checkbox",
        ...props,
        className: `
            h-5 w-5 rounded border-2 border-gray-300 text-red-600 
            focus:ring-2 focus:ring-red-500 focus:ring-offset-0
            transition-all duration-200
            group-hover:border-red-400
            ${className}
          `
      }
    ) }),
    /* @__PURE__ */ jsx("span", { className: "ml-3 text-sm text-gray-700 group-hover:text-gray-900 transition-colors", children: label })
  ] });
}
function StatusMessage({ type, message }) {
  const styles = {
    success: "bg-green-50 border-green-200 text-green-800",
    error: "bg-red-50 border-red-200 text-red-800",
    info: "bg-blue-50 border-blue-200 text-blue-800",
    warning: "bg-yellow-50 border-yellow-200 text-yellow-800"
  };
  const icons = {
    success: /* @__PURE__ */ jsx("svg", { className: "w-5 h-5", fill: "currentColor", viewBox: "0 0 20 20", children: /* @__PURE__ */ jsx("path", { fillRule: "evenodd", d: "M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z", clipRule: "evenodd" }) }),
    error: /* @__PURE__ */ jsx("svg", { className: "w-5 h-5", fill: "currentColor", viewBox: "0 0 20 20", children: /* @__PURE__ */ jsx("path", { fillRule: "evenodd", d: "M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z", clipRule: "evenodd" }) }),
    info: /* @__PURE__ */ jsx("svg", { className: "w-5 h-5", fill: "currentColor", viewBox: "0 0 20 20", children: /* @__PURE__ */ jsx("path", { fillRule: "evenodd", d: "M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z", clipRule: "evenodd" }) }),
    warning: /* @__PURE__ */ jsx("svg", { className: "w-5 h-5", fill: "currentColor", viewBox: "0 0 20 20", children: /* @__PURE__ */ jsx("path", { fillRule: "evenodd", d: "M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z", clipRule: "evenodd" }) })
  };
  return /* @__PURE__ */ jsxs("div", { className: `
      flex items-center p-4 rounded-lg border
      ${styles[type]}
    `, children: [
    /* @__PURE__ */ jsx("div", { className: "flex-shrink-0", children: icons[type] }),
    /* @__PURE__ */ jsx("div", { className: "ml-3", children: /* @__PURE__ */ jsx("p", { className: "text-sm font-medium", children: message }) })
  ] });
}
function AuthNavigation({ type }) {
  return /* @__PURE__ */ jsxs("div", { className: "space-y-4", children: [
    /* @__PURE__ */ jsx("div", { className: "text-center", children: type === "login" ? /* @__PURE__ */ jsxs("p", { className: "text-gray-600", children: [
      "¿No tienes cuenta?",
      " ",
      /* @__PURE__ */ jsx(
        Link,
        {
          href: "/register",
          className: "font-semibold text-red-600 hover:text-red-500 transition-colors",
          children: "Regístrate gratis"
        }
      )
    ] }) : /* @__PURE__ */ jsxs("p", { className: "text-gray-600", children: [
      "¿Ya tienes cuenta?",
      " ",
      /* @__PURE__ */ jsx(
        Link,
        {
          href: "/login",
          className: "font-semibold text-red-600 hover:text-red-500 transition-colors",
          children: "Iniciar sesión"
        }
      )
    ] }) }),
    /* @__PURE__ */ jsx("div", { className: "text-center", children: /* @__PURE__ */ jsxs(
      Link,
      {
        href: "/",
        className: "inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors",
        children: [
          /* @__PURE__ */ jsx("svg", { className: "w-4 h-4 mr-1", fill: "none", stroke: "currentColor", viewBox: "0 0 24 24", children: /* @__PURE__ */ jsx("path", { strokeLinecap: "round", strokeLinejoin: "round", strokeWidth: "2", d: "M10 19l-7-7m0 0l7-7m-7 7h18" }) }),
          "Volver al inicio"
        ]
      }
    ) })
  ] });
}
export {
  AuthLayout as A,
  Button as B,
  Checkbox as C,
  Input as I,
  StatusMessage as S,
  AuthNavigation as a
};
