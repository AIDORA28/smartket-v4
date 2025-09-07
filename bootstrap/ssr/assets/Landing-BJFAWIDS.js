import { jsx, jsxs, Fragment } from "react/jsx-runtime";
import { Link, Head } from "@inertiajs/react";
import { useState } from "react";
function Navbar() {
  return /* @__PURE__ */ jsx("nav", { className: "bg-white shadow-sm sticky top-0 z-50 backdrop-blur-sm bg-white/95", children: /* @__PURE__ */ jsx("div", { className: "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8", children: /* @__PURE__ */ jsxs("div", { className: "flex justify-between h-16", children: [
    /* @__PURE__ */ jsxs("div", { className: "flex items-center", children: [
      /* @__PURE__ */ jsx(
        "img",
        {
          src: "/img/SmartKet.svg",
          alt: "SmartKet",
          className: "h-10 w-auto transition-transform hover:scale-110"
        }
      ),
      /* @__PURE__ */ jsxs("span", { className: "ml-3 text-xl font-bold", children: [
        /* @__PURE__ */ jsx("span", { className: "text-amber-500 uppercase tracking-wide", children: "SMART" }),
        /* @__PURE__ */ jsx("span", { className: "text-gray-900 lowercase", children: "ket" })
      ] })
    ] }),
    /* @__PURE__ */ jsxs("div", { className: "flex items-center space-x-1", children: [
      /* @__PURE__ */ jsx(
        "a",
        {
          href: "#caracteristicas",
          className: "text-gray-600 hover:text-red-600 px-4 py-2 rounded-lg font-medium transition-all duration-300 hover:bg-red-50 hover:scale-105",
          children: "Características"
        }
      ),
      /* @__PURE__ */ jsx(
        "a",
        {
          href: "#precios",
          className: "text-gray-600 hover:text-red-600 px-4 py-2 rounded-lg font-medium transition-all duration-300 hover:bg-red-50 hover:scale-105",
          children: "Precios"
        }
      ),
      /* @__PURE__ */ jsx(
        "a",
        {
          href: "#testimonios",
          className: "text-gray-600 hover:text-red-600 px-4 py-2 rounded-lg font-medium transition-all duration-300 hover:bg-red-50 hover:scale-105",
          children: "Testimonios"
        }
      ),
      /* @__PURE__ */ jsx(Link, { href: "/login", children: /* @__PURE__ */ jsx("button", { className: "text-gray-600 hover:text-red-600 px-6 py-2 rounded-lg font-medium transition-all duration-300 hover:bg-gray-50 hover:scale-105 border border-transparent hover:border-gray-200", children: "Iniciar Sesión" }) }),
      /* @__PURE__ */ jsx(Link, { href: "/register", children: /* @__PURE__ */ jsx("button", { className: "bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-all duration-300 hover:scale-105 hover:shadow-lg shadow-red-200", children: "Prueba Gratis" }) })
    ] })
  ] }) }) });
}
function HeroSection() {
  const scrollToFeatures = () => {
    var _a;
    (_a = document.getElementById("caracteristicas")) == null ? void 0 : _a.scrollIntoView({ behavior: "smooth" });
  };
  return /* @__PURE__ */ jsx("section", { className: "bg-gradient-to-r from-red-700 to-red-800 text-white", children: /* @__PURE__ */ jsx("div", { className: "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24", children: /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 lg:grid-cols-2 gap-12 items-center", children: [
    /* @__PURE__ */ jsxs("div", { children: [
      /* @__PURE__ */ jsxs("h1", { className: "text-4xl lg:text-6xl font-bold mb-6", children: [
        "El ERP más ",
        /* @__PURE__ */ jsx("span", { className: "text-yellow-300", children: "fácil de usar" }),
        " para tu negocio"
      ] }),
      /* @__PURE__ */ jsx("p", { className: "text-xl mb-8 text-red-100", children: "Diseñado especialmente para pequeñas y medianas empresas que quieren un sistema profesional sin complicaciones. ¡Por fin un ERP que todos pueden usar!" }),
      /* @__PURE__ */ jsxs("div", { className: "flex flex-col sm:flex-row gap-4 mb-8", children: [
        /* @__PURE__ */ jsx(Link, { href: "/register", children: /* @__PURE__ */ jsx("button", { className: "w-full sm:w-auto bg-white text-red-700 hover:bg-gray-100 px-8 py-3 rounded-lg font-bold text-lg transition-colors", children: "🚀 Comenzar Gratis" }) }),
        /* @__PURE__ */ jsx(
          "button",
          {
            className: "w-full sm:w-auto border-2 border-white text-white hover:bg-white hover:text-red-700 px-8 py-3 rounded-lg font-bold text-lg transition-colors",
            onClick: scrollToFeatures,
            children: "📋 Ver Características"
          }
        )
      ] }),
      /* @__PURE__ */ jsxs("div", { className: "flex items-center text-sm text-red-100", children: [
        /* @__PURE__ */ jsx("span", { className: "mr-2", children: "✨" }),
        /* @__PURE__ */ jsx("span", { children: "Sin permanencia • Prueba 15 días gratis • Soporte incluido" })
      ] })
    ] }),
    /* @__PURE__ */ jsx("div", { className: "relative", children: /* @__PURE__ */ jsxs("div", { className: "bg-white rounded-lg shadow-2xl p-2 transform rotate-3 hover:rotate-0 transition-transform duration-300", children: [
      /* @__PURE__ */ jsxs(
        "video",
        {
          autoPlay: true,
          muted: true,
          loop: true,
          playsInline: true,
          className: "w-full rounded-lg",
          poster: "/img/image.png",
          children: [
            /* @__PURE__ */ jsx("source", { src: "/video/Minimarket.mp4", type: "video/mp4" }),
            "Tu navegador no soporta el elemento video."
          ]
        }
      ),
      /* @__PURE__ */ jsx("div", { className: "absolute -bottom-2 -right-2 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-lg", children: "▶ Demo en vivo" })
    ] }) })
  ] }) }) });
}
const stats = [
  { value: "500+", label: "Empresas confían en nosotros" },
  { value: "99.9%", label: "Tiempo de actividad" },
  { value: "24/7", label: "Soporte técnico" },
  { value: "15 días", label: "Prueba gratuita" }
];
function StatsSection() {
  return /* @__PURE__ */ jsx("section", { className: "py-16 bg-gray-50", children: /* @__PURE__ */ jsx("div", { className: "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8", children: /* @__PURE__ */ jsx("div", { className: "grid grid-cols-1 md:grid-cols-4 gap-8 text-center", children: stats.map((stat, index) => /* @__PURE__ */ jsxs("div", { children: [
    /* @__PURE__ */ jsx("div", { className: "text-3xl font-bold text-red-600 mb-2", children: stat.value }),
    /* @__PURE__ */ jsx("div", { className: "text-gray-600", children: stat.label })
  ] }, index)) }) }) });
}
function FeatureCard({ icono, titulo, descripcion }) {
  return /* @__PURE__ */ jsxs("div", { className: "bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition-all hover:scale-105 border-t-4 border-red-600", children: [
    /* @__PURE__ */ jsx("div", { className: "text-4xl mb-4", children: icono }),
    /* @__PURE__ */ jsx("h3", { className: "text-xl font-bold mb-4 text-gray-900", children: titulo }),
    /* @__PURE__ */ jsx("p", { className: "text-gray-600", children: descripcion })
  ] });
}
function FeaturesSection({ features }) {
  return /* @__PURE__ */ jsx("section", { id: "caracteristicas", className: "py-20", children: /* @__PURE__ */ jsxs("div", { className: "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8", children: [
    /* @__PURE__ */ jsxs("div", { className: "text-center mb-16", children: [
      /* @__PURE__ */ jsx("h2", { className: "text-3xl font-bold text-gray-900 mb-4", children: "¿Por qué elegir SmartKet?" }),
      /* @__PURE__ */ jsx("p", { className: "text-xl text-gray-600 max-w-3xl mx-auto", children: "Hemos diseñado cada función pensando en la facilidad de uso, para que puedas enfocarte en hacer crecer tu negocio." })
    ] }),
    /* @__PURE__ */ jsx("div", { className: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8", children: features.map((feature, index) => /* @__PURE__ */ jsx(
      FeatureCard,
      {
        icono: feature.icono,
        titulo: feature.titulo,
        descripcion: feature.descripcion
      },
      index
    )) })
  ] }) });
}
function PriceToggle({ billingCycle, onToggle }) {
  const handleToggle = () => {
    onToggle(billingCycle === "mensual" ? "anual" : "mensual");
  };
  return /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-center mb-12", children: [
    /* @__PURE__ */ jsx("span", { className: `mr-3 ${billingCycle === "mensual" ? "text-gray-900 font-semibold" : "text-gray-500"}`, children: "Mensual" }),
    /* @__PURE__ */ jsx(
      "button",
      {
        onClick: handleToggle,
        className: `relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${billingCycle === "anual" ? "bg-red-600" : "bg-gray-300"}`,
        children: /* @__PURE__ */ jsx(
          "span",
          {
            className: `inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${billingCycle === "anual" ? "translate-x-6" : "translate-x-1"}`
          }
        )
      }
    ),
    /* @__PURE__ */ jsx("span", { className: `ml-3 ${billingCycle === "anual" ? "text-gray-900 font-semibold" : "text-gray-500"}`, children: "Anual" }),
    billingCycle === "anual" && /* @__PURE__ */ jsx("span", { className: "ml-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full", children: "Ahorra 20%" })
  ] });
}
function PriceCard({ plan, billingCycle }) {
  const monthlyPrice = billingCycle === "mensual" ? plan.precio : Math.floor(plan.precio_anual / 12);
  const showDiscount = billingCycle === "anual" && plan.descuento_anual > 0;
  return /* @__PURE__ */ jsxs("div", { className: `bg-white rounded-lg shadow-lg p-8 relative transition-all hover:scale-105 flex flex-col h-full ${plan.popular ? "ring-2 ring-red-500 shadow-xl scale-105" : ""}`, children: [
    plan.popular && /* @__PURE__ */ jsx("div", { className: "absolute -top-4 left-1/2 transform -translate-x-1/2", children: /* @__PURE__ */ jsx("span", { className: "bg-red-600 text-white px-4 py-1 rounded-full text-sm font-semibold", children: "⭐ Más Popular" }) }),
    /* @__PURE__ */ jsxs("div", { className: "text-center mb-8 flex-shrink-0", children: [
      /* @__PURE__ */ jsx("h3", { className: "text-2xl font-bold text-gray-900 mb-2", children: plan.nombre }),
      /* @__PURE__ */ jsx("p", { className: "text-gray-600 mb-4", children: plan.descripcion }),
      /* @__PURE__ */ jsx("div", { className: "mb-4", children: plan.es_gratis ? /* @__PURE__ */ jsxs("div", { children: [
        /* @__PURE__ */ jsx("span", { className: "text-4xl font-bold text-green-600", children: "GRATIS" }),
        /* @__PURE__ */ jsx("p", { className: "text-sm text-gray-600 mt-2", children: "Para siempre" })
      ] }) : /* @__PURE__ */ jsxs("div", { children: [
        /* @__PURE__ */ jsxs("span", { className: "text-4xl font-bold text-gray-900", children: [
          "S/ ",
          monthlyPrice
        ] }),
        /* @__PURE__ */ jsxs("span", { className: "text-gray-600 ml-2", children: [
          "/",
          billingCycle === "mensual" ? "mes" : "mes"
        ] }),
        billingCycle === "anual" && /* @__PURE__ */ jsxs("div", { className: "mt-2", children: [
          /* @__PURE__ */ jsxs("p", { className: "text-sm text-gray-600", children: [
            "S/ ",
            plan.precio_anual,
            " facturado anualmente"
          ] }),
          showDiscount && /* @__PURE__ */ jsxs("p", { className: "text-sm text-green-600 font-medium", children: [
            "Ahorras ",
            plan.descuento_anual,
            "%"
          ] })
        ] }),
        plan.dias_prueba > 0 && /* @__PURE__ */ jsxs("p", { className: "text-sm text-blue-600 font-medium mt-2", children: [
          plan.dias_prueba,
          " días de prueba gratis"
        ] })
      ] }) })
    ] }),
    /* @__PURE__ */ jsxs("div", { className: "bg-gray-50 rounded-lg p-4 mb-6 flex-shrink-0", children: [
      /* @__PURE__ */ jsx("h4", { className: "text-sm font-semibold text-gray-900 mb-2", children: "Límites incluidos:" }),
      /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-2 gap-2 text-xs text-gray-600", children: [
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
          plan.max_productos,
          " productos"
        ] })
      ] })
    ] }),
    /* @__PURE__ */ jsx("ul", { className: "space-y-3 mb-8 flex-grow", children: plan.features.map((feature, index) => /* @__PURE__ */ jsxs("li", { className: "flex items-start", children: [
      /* @__PURE__ */ jsx("span", { className: "text-red-500 mr-2 flex-shrink-0 mt-1", children: "✓" }),
      /* @__PURE__ */ jsx("span", { className: "text-gray-600 text-sm", children: feature })
    ] }, index)) }),
    /* @__PURE__ */ jsx("div", { className: "mt-auto", children: /* @__PURE__ */ jsx(Link, { href: `/register?plan=${plan.id}`, className: "block", children: /* @__PURE__ */ jsx(
      "button",
      {
        className: `w-full py-3 px-6 rounded-lg font-semibold transition-colors ${plan.popular ? "bg-red-600 hover:bg-red-700 text-white" : plan.es_gratis ? "bg-green-600 hover:bg-green-700 text-white" : "border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white"}`,
        children: plan.es_gratis ? "Comenzar Gratis" : "Probar Gratis"
      }
    ) }) })
  ] });
}
function PricingSection({ planes }) {
  const [billingCycle, setBillingCycle] = useState("mensual");
  return /* @__PURE__ */ jsx("section", { id: "precios", className: "py-20 bg-gray-50", children: /* @__PURE__ */ jsxs("div", { className: "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8", children: [
    /* @__PURE__ */ jsxs("div", { className: "text-center mb-12", children: [
      /* @__PURE__ */ jsx("h2", { className: "text-3xl font-bold text-gray-900 mb-4", children: "Precios transparentes y justos" }),
      /* @__PURE__ */ jsx("p", { className: "text-xl text-gray-600 mb-8", children: "Elige el plan que mejor se adapte a tu negocio. Todos incluyen prueba gratuita." }),
      /* @__PURE__ */ jsx(
        PriceToggle,
        {
          billingCycle,
          onToggle: setBillingCycle
        }
      )
    ] }),
    /* @__PURE__ */ jsx("div", { className: "grid grid-cols-1 md:grid-cols-3 gap-8", children: planes.map((plan) => /* @__PURE__ */ jsx(
      PriceCard,
      {
        plan,
        billingCycle
      },
      plan.id
    )) })
  ] }) });
}
function TestimonialCard({ testimonio }) {
  return /* @__PURE__ */ jsxs("div", { className: "bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-all", children: [
    /* @__PURE__ */ jsx("div", { className: "flex mb-4", children: Array.from({ length: testimonio.rating }).map((_, i) => /* @__PURE__ */ jsx("span", { className: "text-yellow-400 text-xl", children: "⭐" }, i)) }),
    /* @__PURE__ */ jsxs("blockquote", { className: "text-gray-600 mb-6 italic", children: [
      '"',
      testimonio.testimonio,
      '"'
    ] }),
    /* @__PURE__ */ jsxs("div", { className: "flex items-center", children: [
      /* @__PURE__ */ jsx(
        "img",
        {
          src: testimonio.avatar,
          alt: testimonio.nombre,
          className: "w-12 h-12 rounded-full object-cover mr-4"
        }
      ),
      /* @__PURE__ */ jsxs("div", { children: [
        /* @__PURE__ */ jsx("div", { className: "font-semibold text-gray-900", children: testimonio.nombre }),
        /* @__PURE__ */ jsx("div", { className: "text-sm text-red-600 font-medium", children: testimonio.negocio })
      ] })
    ] })
  ] });
}
function TestimonialsSection({ testimonios }) {
  return /* @__PURE__ */ jsx("section", { id: "testimonios", className: "py-20", children: /* @__PURE__ */ jsxs("div", { className: "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8", children: [
    /* @__PURE__ */ jsxs("div", { className: "text-center mb-16", children: [
      /* @__PURE__ */ jsx("h2", { className: "text-3xl font-bold text-gray-900 mb-4", children: "Lo que dicen nuestros clientes" }),
      /* @__PURE__ */ jsx("p", { className: "text-xl text-gray-600", children: "Más de 500 negocios confían en SmartKet para crecer cada día" })
    ] }),
    /* @__PURE__ */ jsx("div", { className: "grid grid-cols-1 md:grid-cols-3 gap-8", children: testimonios.map((testimonio, index) => /* @__PURE__ */ jsx(
      TestimonialCard,
      {
        testimonio
      },
      index
    )) })
  ] }) });
}
function CTASection() {
  return /* @__PURE__ */ jsx("section", { className: "bg-red-600 text-white py-20", children: /* @__PURE__ */ jsxs("div", { className: "max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center", children: [
    /* @__PURE__ */ jsx("h2", { className: "text-3xl font-bold mb-4", children: "¿Listo para hacer crecer tu negocio?" }),
    /* @__PURE__ */ jsx("p", { className: "text-xl mb-8 text-red-100", children: "Únete a cientos de empresas que ya están usando SmartKet para gestionar su negocio de manera inteligente." }),
    /* @__PURE__ */ jsx(Link, { href: "/register", children: /* @__PURE__ */ jsx("button", { className: "bg-white text-red-600 hover:bg-gray-100 px-8 py-3 rounded-lg font-bold text-lg transition-colors mr-4", children: "🚀 Comenzar Prueba Gratis" }) }),
    /* @__PURE__ */ jsx("p", { className: "text-sm text-red-100 mt-4", children: "15 días gratis • Sin tarjeta de crédito requerida • Soporte incluido" })
  ] }) });
}
function Footer() {
  return /* @__PURE__ */ jsx("footer", { className: "bg-gray-900 text-white py-12", children: /* @__PURE__ */ jsxs("div", { className: "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8", children: [
    /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 md:grid-cols-4 gap-8", children: [
      /* @__PURE__ */ jsxs("div", { children: [
        /* @__PURE__ */ jsxs("div", { className: "flex items-center mb-4", children: [
          /* @__PURE__ */ jsx(
            "img",
            {
              src: "/img/SmartKet.svg",
              alt: "SmartKet",
              className: "h-8 w-auto"
            }
          ),
          /* @__PURE__ */ jsx("span", { className: "ml-2 text-xl font-bold", children: "SmartKet" })
        ] }),
        /* @__PURE__ */ jsx("p", { className: "text-gray-400", children: "ERP intuitivo diseñado para pequeñas y medianas empresas." })
      ] }),
      /* @__PURE__ */ jsxs("div", { children: [
        /* @__PURE__ */ jsx("h4", { className: "font-semibold mb-4 text-red-400", children: "Producto" }),
        /* @__PURE__ */ jsxs("ul", { className: "space-y-2 text-gray-400", children: [
          /* @__PURE__ */ jsx("li", { children: /* @__PURE__ */ jsx("a", { href: "#caracteristicas", className: "hover:text-white transition-colors", children: "Características" }) }),
          /* @__PURE__ */ jsx("li", { children: /* @__PURE__ */ jsx("a", { href: "#precios", className: "hover:text-white transition-colors", children: "Precios" }) }),
          /* @__PURE__ */ jsx("li", { children: /* @__PURE__ */ jsx(Link, { href: "/login", className: "hover:text-white transition-colors", children: "Demo" }) })
        ] })
      ] }),
      /* @__PURE__ */ jsxs("div", { children: [
        /* @__PURE__ */ jsx("h4", { className: "font-semibold mb-4 text-red-400", children: "Soporte" }),
        /* @__PURE__ */ jsxs("ul", { className: "space-y-2 text-gray-400", children: [
          /* @__PURE__ */ jsx("li", { children: /* @__PURE__ */ jsx("a", { href: "#", className: "hover:text-white transition-colors", children: "Centro de Ayuda" }) }),
          /* @__PURE__ */ jsx("li", { children: /* @__PURE__ */ jsx("a", { href: "#", className: "hover:text-white transition-colors", children: "Contacto" }) }),
          /* @__PURE__ */ jsx("li", { children: /* @__PURE__ */ jsx("a", { href: "#", className: "hover:text-white transition-colors", children: "Capacitación" }) })
        ] })
      ] }),
      /* @__PURE__ */ jsxs("div", { children: [
        /* @__PURE__ */ jsx("h4", { className: "font-semibold mb-4 text-red-400", children: "Empresa" }),
        /* @__PURE__ */ jsxs("ul", { className: "space-y-2 text-gray-400", children: [
          /* @__PURE__ */ jsx("li", { children: /* @__PURE__ */ jsx("a", { href: "#", className: "hover:text-white transition-colors", children: "Acerca de" }) }),
          /* @__PURE__ */ jsx("li", { children: /* @__PURE__ */ jsx("a", { href: "#", className: "hover:text-white transition-colors", children: "Blog" }) }),
          /* @__PURE__ */ jsx("li", { children: /* @__PURE__ */ jsx("a", { href: "#", className: "hover:text-white transition-colors", children: "Términos" }) }),
          /* @__PURE__ */ jsx("li", { children: /* @__PURE__ */ jsx("a", { href: "#", className: "hover:text-white transition-colors", children: "Privacidad" }) })
        ] })
      ] })
    ] }),
    /* @__PURE__ */ jsx("div", { className: "border-t border-gray-800 mt-12 pt-8 text-center text-gray-400", children: /* @__PURE__ */ jsx("p", { children: "© 2025 SmartKet. Todos los derechos reservados." }) })
  ] }) });
}
function Landing({ planes, features, testimonios }) {
  return /* @__PURE__ */ jsxs(Fragment, { children: [
    /* @__PURE__ */ jsx(Head, { title: "SmartKet - ERP Intuitivo para PyMEs" }),
    /* @__PURE__ */ jsx(Navbar, {}),
    /* @__PURE__ */ jsx(HeroSection, {}),
    /* @__PURE__ */ jsx(StatsSection, {}),
    /* @__PURE__ */ jsx(FeaturesSection, { features }),
    /* @__PURE__ */ jsx(PricingSection, { planes }),
    /* @__PURE__ */ jsx(TestimonialsSection, { testimonios }),
    /* @__PURE__ */ jsx(CTASection, {}),
    /* @__PURE__ */ jsx(Footer, {})
  ] });
}
export {
  Landing as default
};
