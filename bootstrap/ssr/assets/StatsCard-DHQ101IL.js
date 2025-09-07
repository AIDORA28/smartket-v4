import { jsx, jsxs } from "react/jsx-runtime";
import { clsx } from "clsx";
import { ChartBarIcon, ArchiveBoxIcon, ArrowTrendingDownIcon, ArrowTrendingUpIcon, BanknotesIcon, UserGroupIcon, ExclamationTriangleIcon, ShoppingCartIcon } from "@heroicons/react/24/outline";
const iconMap = {
  cart: ShoppingCartIcon,
  warning: ExclamationTriangleIcon,
  users: UserGroupIcon,
  money: BanknotesIcon,
  up: ArrowTrendingUpIcon,
  down: ArrowTrendingDownIcon,
  products: ArchiveBoxIcon,
  chart: ChartBarIcon
};
const colorMap = {
  blue: {
    bg: "bg-blue-50",
    icon: "text-blue-600",
    text: "text-blue-900"
  },
  red: {
    bg: "bg-red-50",
    icon: "text-red-600",
    text: "text-red-900"
  },
  green: {
    bg: "bg-green-50",
    icon: "text-green-600",
    text: "text-green-900"
  },
  purple: {
    bg: "bg-purple-50",
    icon: "text-purple-600",
    text: "text-purple-900"
  },
  yellow: {
    bg: "bg-yellow-50",
    icon: "text-yellow-600",
    text: "text-yellow-900"
  },
  gray: {
    bg: "bg-gray-50",
    icon: "text-gray-600",
    text: "text-gray-900"
  },
  orange: {
    bg: "bg-orange-50",
    icon: "text-orange-600",
    text: "text-orange-900"
  }
};
function StatsCard({ title, value, icon, color, trend, subtitle, className }) {
  const IconComponent = iconMap[icon];
  const colors = colorMap[color];
  return /* @__PURE__ */ jsx("div", { className: clsx(
    "bg-white overflow-hidden shadow-lg rounded-xl border border-gray-200 hover:shadow-xl transition-shadow duration-300",
    className
  ), children: /* @__PURE__ */ jsx("div", { className: "p-6", children: /* @__PURE__ */ jsxs("div", { className: "flex items-center", children: [
    /* @__PURE__ */ jsx("div", { className: clsx("flex-shrink-0 p-3 rounded-xl shadow-sm", colors.bg), children: IconComponent ? /* @__PURE__ */ jsx(IconComponent, { className: clsx("h-7 w-7", colors.icon) }) : /* @__PURE__ */ jsx("span", { className: "text-2xl", children: icon }) }),
    /* @__PURE__ */ jsxs("div", { className: "ml-5 flex-1", children: [
      /* @__PURE__ */ jsxs("div", { className: "flex items-baseline justify-between", children: [
        /* @__PURE__ */ jsxs("div", { children: [
          /* @__PURE__ */ jsx("p", { className: "text-sm font-medium text-gray-600 truncate", children: title }),
          /* @__PURE__ */ jsx("p", { className: clsx("text-3xl font-bold mt-1", colors.text), children: value }),
          subtitle && /* @__PURE__ */ jsx("p", { className: "text-xs text-gray-500 mt-1", children: subtitle })
        ] }),
        trend && /* @__PURE__ */ jsxs("div", { className: clsx(
          "inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold",
          trend.direction === "up" ? "bg-green-100 text-green-800 shadow-sm" : "bg-red-100 text-red-800 shadow-sm"
        ), children: [
          trend.direction === "up" ? /* @__PURE__ */ jsx(ArrowTrendingUpIcon, { className: "h-4 w-4 mr-1" }) : /* @__PURE__ */ jsx(ArrowTrendingDownIcon, { className: "h-4 w-4 mr-1" }),
          trend.value,
          "%"
        ] })
      ] }),
      trend && /* @__PURE__ */ jsx("p", { className: "mt-2 text-sm text-gray-600 font-medium", children: trend.label })
    ] })
  ] }) }) });
}
export {
  StatsCard as S
};
