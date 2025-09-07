import { jsxs, jsx } from "react/jsx-runtime";
import { clsx } from "clsx";
const Spinner = ({ className }) => /* @__PURE__ */ jsxs(
  "svg",
  {
    className: clsx("animate-spin h-4 w-4", className),
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
);
function Button({
  variant = "primary",
  size = "md",
  loading = false,
  children,
  fullWidth = false,
  disabled,
  className,
  ...props
}) {
  const baseClasses = "inline-flex items-center justify-center rounded-md font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed";
  const variants = {
    primary: "bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500",
    secondary: "bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500",
    success: "bg-green-600 text-white hover:bg-green-700 focus:ring-green-500",
    danger: "bg-red-600 text-white hover:bg-red-700 focus:ring-red-500",
    warning: "bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500",
    ghost: "bg-transparent text-gray-700 hover:bg-gray-100 focus:ring-gray-500"
  };
  const sizes = {
    sm: "px-3 py-2 text-sm",
    md: "px-4 py-2 text-base",
    lg: "px-6 py-3 text-lg"
  };
  return /* @__PURE__ */ jsxs(
    "button",
    {
      className: clsx(
        baseClasses,
        variants[variant],
        sizes[size],
        fullWidth && "w-full",
        className
      ),
      disabled: loading || disabled,
      ...props,
      children: [
        loading && /* @__PURE__ */ jsx(Spinner, { className: "mr-2" }),
        children
      ]
    }
  );
}
function Card({ children, className, padding = "md", onClick }) {
  const paddingClasses = {
    none: "",
    sm: "p-4",
    md: "p-6",
    lg: "p-8"
  };
  return /* @__PURE__ */ jsx(
    "div",
    {
      className: clsx(
        "bg-white rounded-lg shadow border border-gray-200",
        paddingClasses[padding],
        onClick && "cursor-pointer hover:shadow-md transition-shadow",
        className
      ),
      onClick,
      children
    }
  );
}
function CardHeader({ children, className }) {
  return /* @__PURE__ */ jsx("div", { className: clsx("border-b border-gray-200 pb-4 mb-4", className), children });
}
function CardBody({ children, className }) {
  return /* @__PURE__ */ jsx("div", { className: clsx("", className), children });
}
export {
  Button as B,
  Card as C,
  CardHeader as a,
  CardBody as b
};
