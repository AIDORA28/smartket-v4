import { jsxs, jsx } from "react/jsx-runtime";
function Card({ children, className = "", title }) {
  return /* @__PURE__ */ jsxs("div", { className: `bg-white overflow-hidden shadow-sm sm:rounded-lg ${className}`, children: [
    title && /* @__PURE__ */ jsx("div", { className: "px-6 py-4 border-b border-gray-200", children: /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900", children: title }) }),
    /* @__PURE__ */ jsx("div", { className: "px-6 py-4", children })
  ] });
}
function Button({
  variant = "primary",
  size = "md",
  children,
  className = "",
  disabled,
  ...props
}) {
  const baseClasses = "inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200";
  const variants = {
    primary: "bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500",
    secondary: "bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500",
    outline: "bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-blue-500",
    danger: "bg-red-600 text-white hover:bg-red-700 focus:ring-red-500"
  };
  const sizes = {
    sm: "px-3 py-2 text-sm",
    md: "px-4 py-2 text-sm",
    lg: "px-6 py-3 text-base"
  };
  const disabledClasses = disabled ? "opacity-50 cursor-not-allowed" : "";
  return /* @__PURE__ */ jsx(
    "button",
    {
      className: `${baseClasses} ${variants[variant]} ${sizes[size]} ${disabledClasses} ${className}`,
      disabled,
      ...props,
      children
    }
  );
}
export {
  Button as B,
  Card as C
};
