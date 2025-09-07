import { jsxs, jsx } from "react/jsx-runtime";
import { useState } from "react";
import { Head, router } from "@inertiajs/react";
import { clsx } from "clsx";
import { A as AuthenticatedLayout } from "./AuthenticatedLayout-BfjI-KKz.js";
import { C as Card, b as CardBody, a as CardHeader, B as Button } from "./Card-DoF5IT7l.js";
import { MagnifyingGlassIcon, UserIcon, XMarkIcon, MinusIcon, PlusIcon, ShoppingCartIcon, BanknotesIcon, CreditCardIcon, CalculatorIcon, PrinterIcon } from "@heroicons/react/24/outline";
function POS({ auth, products, clients, categories }) {
  const [cart, setCart] = useState([]);
  const [selectedClient, setSelectedClient] = useState(null);
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedCategory, setSelectedCategory] = useState("all");
  const [paymentMethod, setPaymentMethod] = useState("cash");
  const [showClientModal, setShowClientModal] = useState(false);
  const [clientSearch, setClientSearch] = useState("");
  const [amountPaid, setAmountPaid] = useState("");
  const filteredProducts = products.filter((product) => {
    const matchesSearch = product.nombre.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = selectedCategory === "all" || product.categoria === selectedCategory;
    return matchesSearch && matchesCategory && product.stock > 0;
  });
  const cartTotal = cart.reduce((total, item) => total + item.subtotal, 0);
  const cartItemsCount = cart.reduce((total, item) => total + item.cantidad, 0);
  const addToCart = (product) => {
    const existingItem = cart.find((item) => item.id === product.id);
    if (existingItem) {
      if (existingItem.cantidad < product.stock) {
        setCart(cart.map(
          (item) => item.id === product.id ? { ...item, cantidad: item.cantidad + 1, subtotal: (item.cantidad + 1) * item.precio } : item
        ));
      }
    } else {
      const newItem = {
        id: product.id,
        nombre: product.nombre,
        precio: product.precio,
        cantidad: 1,
        subtotal: product.precio
      };
      setCart([...cart, newItem]);
    }
  };
  const updateCartQuantity = (itemId, newQuantity) => {
    if (newQuantity <= 0) {
      removeFromCart(itemId);
      return;
    }
    const product = products.find((p) => p.id === itemId);
    if (product && newQuantity <= product.stock) {
      setCart(cart.map(
        (item) => item.id === itemId ? { ...item, cantidad: newQuantity, subtotal: newQuantity * item.precio } : item
      ));
    }
  };
  const removeFromCart = (itemId) => {
    setCart(cart.filter((item) => item.id !== itemId));
  };
  const clearCart = () => {
    setCart([]);
    setSelectedClient(null);
    setAmountPaid("");
  };
  const processSale = () => {
    if (cart.length === 0) return;
    const saleData = {
      cliente_id: (selectedClient == null ? void 0 : selectedClient.id) || null,
      items: JSON.stringify(cart),
      total: cartTotal.toString(),
      metodo_pago: paymentMethod,
      monto_pagado: (paymentMethod === "cash" ? parseFloat(amountPaid) || cartTotal : cartTotal).toString()
    };
    router.post("/pos/process-sale", saleData, {
      onSuccess: () => {
        clearCart();
      },
      onError: (errors) => {
        console.error("Error processing sale:", errors);
      }
    });
  };
  const change = paymentMethod === "cash" && amountPaid ? parseFloat(amountPaid) - cartTotal : 0;
  return /* @__PURE__ */ jsxs(
    AuthenticatedLayout,
    {
      header: /* @__PURE__ */ jsxs("div", { className: "flex justify-between items-center", children: [
        /* @__PURE__ */ jsx("h2", { className: "font-semibold text-xl text-gray-800 leading-tight", children: "Punto de Venta" }),
        /* @__PURE__ */ jsxs("div", { className: "flex items-center gap-4", children: [
          /* @__PURE__ */ jsxs("div", { className: "text-sm text-gray-600", children: [
            "Cajero: ",
            /* @__PURE__ */ jsx("span", { className: "font-medium", children: auth.user.name })
          ] }),
          /* @__PURE__ */ jsx("div", { className: "text-sm text-gray-600", children: (/* @__PURE__ */ new Date()).toLocaleString() })
        ] })
      ] }),
      children: [
        /* @__PURE__ */ jsx(Head, { title: "Punto de Venta" }),
        /* @__PURE__ */ jsx("div", { className: "py-6", children: /* @__PURE__ */ jsx("div", { className: "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8", children: /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-1 lg:grid-cols-3 gap-6", children: [
          /* @__PURE__ */ jsxs("div", { className: "lg:col-span-2 space-y-6", children: [
            /* @__PURE__ */ jsx(Card, { children: /* @__PURE__ */ jsx(CardBody, { children: /* @__PURE__ */ jsxs("div", { className: "flex flex-col sm:flex-row gap-4", children: [
              /* @__PURE__ */ jsxs("div", { className: "flex-1 relative", children: [
                /* @__PURE__ */ jsx(MagnifyingGlassIcon, { className: "absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" }),
                /* @__PURE__ */ jsx(
                  "input",
                  {
                    type: "text",
                    placeholder: "Buscar productos...",
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
                  value: selectedCategory,
                  onChange: (e) => setSelectedCategory(e.target.value),
                  children: [
                    /* @__PURE__ */ jsx("option", { value: "all", children: "Todas las categorías" }),
                    categories.map((category) => /* @__PURE__ */ jsx("option", { value: category, children: category }, category))
                  ]
                }
              )
            ] }) }) }),
            /* @__PURE__ */ jsx("div", { className: "grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4", children: filteredProducts.map((product) => /* @__PURE__ */ jsxs(
              "div",
              {
                className: "bg-white rounded-lg shadow border cursor-pointer hover:shadow-md transition-shadow",
                onClick: () => addToCart(product),
                children: [
                  /* @__PURE__ */ jsx("div", { className: "aspect-square bg-gray-100 rounded-t-lg flex items-center justify-center", children: product.imagen ? /* @__PURE__ */ jsx(
                    "img",
                    {
                      src: product.imagen,
                      alt: product.nombre,
                      className: "w-full h-full object-cover rounded-t-lg"
                    }
                  ) : /* @__PURE__ */ jsx("span", { className: "text-4xl text-gray-400", children: "📦" }) }),
                  /* @__PURE__ */ jsxs("div", { className: "p-3", children: [
                    /* @__PURE__ */ jsx("h3", { className: "font-medium text-sm text-gray-900 mb-1 line-clamp-2", children: product.nombre }),
                    /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
                      /* @__PURE__ */ jsxs("span", { className: "text-lg font-bold text-green-600", children: [
                        "$",
                        product.precio.toLocaleString()
                      ] }),
                      /* @__PURE__ */ jsxs("span", { className: "text-xs text-gray-500", children: [
                        "Stock: ",
                        product.stock
                      ] })
                    ] })
                  ] })
                ]
              },
              product.id
            )) }),
            filteredProducts.length === 0 && /* @__PURE__ */ jsxs("div", { className: "text-center py-12 bg-white rounded-lg shadow border", children: [
              /* @__PURE__ */ jsx("div", { className: "text-gray-400 text-6xl mb-4", children: "🔍" }),
              /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900 mb-2", children: "No se encontraron productos" }),
              /* @__PURE__ */ jsx("p", { className: "text-gray-500", children: "Intenta cambiar los filtros de búsqueda" })
            ] })
          ] }),
          /* @__PURE__ */ jsxs("div", { className: "space-y-6", children: [
            /* @__PURE__ */ jsxs(Card, { children: [
              /* @__PURE__ */ jsx(CardHeader, { children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
                /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900", children: "Cliente" }),
                /* @__PURE__ */ jsxs(
                  Button,
                  {
                    variant: "ghost",
                    size: "sm",
                    onClick: () => setShowClientModal(true),
                    children: [
                      /* @__PURE__ */ jsx(UserIcon, { className: "w-4 h-4 mr-2" }),
                      "Seleccionar"
                    ]
                  }
                )
              ] }) }),
              /* @__PURE__ */ jsx(CardBody, { children: selectedClient ? /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
                /* @__PURE__ */ jsxs("div", { children: [
                  /* @__PURE__ */ jsx("p", { className: "font-medium text-gray-900", children: selectedClient.nombre }),
                  selectedClient.telefono && /* @__PURE__ */ jsx("p", { className: "text-sm text-gray-500", children: selectedClient.telefono })
                ] }),
                /* @__PURE__ */ jsx(
                  Button,
                  {
                    variant: "ghost",
                    size: "sm",
                    onClick: () => setSelectedClient(null),
                    children: /* @__PURE__ */ jsx(XMarkIcon, { className: "w-4 h-4" })
                  }
                )
              ] }) : /* @__PURE__ */ jsx("p", { className: "text-gray-500 text-center py-4", children: "Cliente general" }) })
            ] }),
            /* @__PURE__ */ jsxs(Card, { children: [
              /* @__PURE__ */ jsx(CardHeader, { children: /* @__PURE__ */ jsxs("div", { className: "flex items-center justify-between", children: [
                /* @__PURE__ */ jsxs("h3", { className: "text-lg font-medium text-gray-900", children: [
                  "Carrito (",
                  cartItemsCount,
                  ")"
                ] }),
                cart.length > 0 && /* @__PURE__ */ jsx(
                  Button,
                  {
                    variant: "ghost",
                    size: "sm",
                    onClick: clearCart,
                    className: "text-red-600 hover:text-red-700",
                    children: "Limpiar"
                  }
                )
              ] }) }),
              /* @__PURE__ */ jsx(CardBody, { children: /* @__PURE__ */ jsxs("div", { className: "space-y-3", children: [
                cart.map((item) => /* @__PURE__ */ jsxs("div", { className: "flex items-center gap-3 p-3 bg-gray-50 rounded-lg", children: [
                  /* @__PURE__ */ jsxs("div", { className: "flex-1", children: [
                    /* @__PURE__ */ jsx("p", { className: "font-medium text-sm text-gray-900", children: item.nombre }),
                    /* @__PURE__ */ jsxs("p", { className: "text-sm text-gray-500", children: [
                      "$",
                      item.precio.toLocaleString(),
                      " c/u"
                    ] })
                  ] }),
                  /* @__PURE__ */ jsxs("div", { className: "flex items-center gap-2", children: [
                    /* @__PURE__ */ jsx(
                      Button,
                      {
                        variant: "ghost",
                        size: "sm",
                        onClick: () => updateCartQuantity(item.id, item.cantidad - 1),
                        children: /* @__PURE__ */ jsx(MinusIcon, { className: "w-4 h-4" })
                      }
                    ),
                    /* @__PURE__ */ jsx("span", { className: "w-8 text-center font-medium", children: item.cantidad }),
                    /* @__PURE__ */ jsx(
                      Button,
                      {
                        variant: "ghost",
                        size: "sm",
                        onClick: () => updateCartQuantity(item.id, item.cantidad + 1),
                        children: /* @__PURE__ */ jsx(PlusIcon, { className: "w-4 h-4" })
                      }
                    )
                  ] }),
                  /* @__PURE__ */ jsxs("div", { className: "text-right", children: [
                    /* @__PURE__ */ jsxs("p", { className: "font-medium text-gray-900", children: [
                      "$",
                      item.subtotal.toLocaleString()
                    ] }),
                    /* @__PURE__ */ jsx(
                      Button,
                      {
                        variant: "ghost",
                        size: "sm",
                        onClick: () => removeFromCart(item.id),
                        className: "text-red-600 hover:text-red-700",
                        children: /* @__PURE__ */ jsx(XMarkIcon, { className: "w-4 h-4" })
                      }
                    )
                  ] })
                ] }, item.id)),
                cart.length === 0 && /* @__PURE__ */ jsxs("div", { className: "text-center py-8 text-gray-500", children: [
                  /* @__PURE__ */ jsx(ShoppingCartIcon, { className: "w-12 h-12 mx-auto mb-3 text-gray-300" }),
                  /* @__PURE__ */ jsx("p", { children: "El carrito está vacío" }),
                  /* @__PURE__ */ jsx("p", { className: "text-sm", children: "Selecciona productos para comenzar" })
                ] })
              ] }) })
            ] }),
            cart.length > 0 && /* @__PURE__ */ jsxs(Card, { children: [
              /* @__PURE__ */ jsx(CardHeader, { children: /* @__PURE__ */ jsx("h3", { className: "text-lg font-medium text-gray-900", children: "Pago" }) }),
              /* @__PURE__ */ jsx(CardBody, { children: /* @__PURE__ */ jsxs("div", { className: "space-y-4", children: [
                /* @__PURE__ */ jsx("div", { className: "border-t border-gray-200 pt-4", children: /* @__PURE__ */ jsxs("div", { className: "flex justify-between text-xl font-bold text-gray-900", children: [
                  /* @__PURE__ */ jsx("span", { children: "Total" }),
                  /* @__PURE__ */ jsxs("span", { children: [
                    "$",
                    cartTotal.toLocaleString()
                  ] })
                ] }) }),
                /* @__PURE__ */ jsxs("div", { className: "space-y-2", children: [
                  /* @__PURE__ */ jsx("label", { className: "block text-sm font-medium text-gray-700", children: "Método de pago" }),
                  /* @__PURE__ */ jsxs("div", { className: "grid grid-cols-2 gap-2", children: [
                    /* @__PURE__ */ jsxs(
                      "button",
                      {
                        type: "button",
                        className: clsx(
                          "flex items-center justify-center px-3 py-2 border rounded-md text-sm font-medium",
                          paymentMethod === "cash" ? "border-blue-500 bg-blue-50 text-blue-700" : "border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
                        ),
                        onClick: () => setPaymentMethod("cash"),
                        children: [
                          /* @__PURE__ */ jsx(BanknotesIcon, { className: "w-4 h-4 mr-2" }),
                          "Efectivo"
                        ]
                      }
                    ),
                    /* @__PURE__ */ jsxs(
                      "button",
                      {
                        type: "button",
                        className: clsx(
                          "flex items-center justify-center px-3 py-2 border rounded-md text-sm font-medium",
                          paymentMethod === "card" ? "border-blue-500 bg-blue-50 text-blue-700" : "border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
                        ),
                        onClick: () => setPaymentMethod("card"),
                        children: [
                          /* @__PURE__ */ jsx(CreditCardIcon, { className: "w-4 h-4 mr-2" }),
                          "Tarjeta"
                        ]
                      }
                    )
                  ] })
                ] }),
                paymentMethod === "cash" && /* @__PURE__ */ jsxs("div", { className: "space-y-2", children: [
                  /* @__PURE__ */ jsx("label", { className: "block text-sm font-medium text-gray-700", children: "Monto pagado" }),
                  /* @__PURE__ */ jsx(
                    "input",
                    {
                      type: "number",
                      step: "0.01",
                      min: cartTotal,
                      className: "w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500",
                      value: amountPaid,
                      onChange: (e) => setAmountPaid(e.target.value),
                      placeholder: cartTotal.toString()
                    }
                  ),
                  change > 0 && /* @__PURE__ */ jsxs("div", { className: "text-sm", children: [
                    /* @__PURE__ */ jsx("span", { className: "text-gray-600", children: "Cambio: " }),
                    /* @__PURE__ */ jsxs("span", { className: "font-medium text-green-600", children: [
                      "$",
                      change.toLocaleString()
                    ] })
                  ] })
                ] }),
                /* @__PURE__ */ jsxs(
                  Button,
                  {
                    variant: "primary",
                    size: "lg",
                    className: "w-full",
                    onClick: processSale,
                    disabled: paymentMethod === "cash" && amountPaid !== "" && parseFloat(amountPaid) < cartTotal,
                    children: [
                      /* @__PURE__ */ jsx(CalculatorIcon, { className: "w-5 h-5 mr-2" }),
                      "Procesar Venta"
                    ]
                  }
                ),
                /* @__PURE__ */ jsxs(
                  Button,
                  {
                    variant: "secondary",
                    size: "lg",
                    className: "w-full",
                    onClick: () => {
                    },
                    children: [
                      /* @__PURE__ */ jsx(PrinterIcon, { className: "w-5 h-5 mr-2" }),
                      "Vista Previa Ticket"
                    ]
                  }
                )
              ] }) })
            ] })
          ] })
        ] }) }) })
      ]
    }
  );
}
export {
  POS as default
};
