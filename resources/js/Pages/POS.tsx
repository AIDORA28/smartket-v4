import React, { useState, useEffect } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import { clsx } from 'clsx';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout';
import { Button } from '../Components/ui/Button';
import { Card, CardHeader, CardBody } from '../Components/ui/Card';
import MetricCard from '@/Components/core/shared/MetricCard';
import ActionCard from '@/Components/core/shared/ActionCard';
import {
  PlusIcon,
  MinusIcon,
  XMarkIcon,
  ShoppingCartIcon,
  CreditCardIcon,
  BanknotesIcon,
  PrinterIcon,
  UserIcon,
  MagnifyingGlassIcon,
  CalculatorIcon
} from '@heroicons/react/24/outline';

interface Product {
  id: number;
  nombre: string;
  precio: number;
  stock: number;
  categoria: string;
  imagen?: string;
}

interface CartItem {
  id: number;
  nombre: string;
  precio: number;
  cantidad: number;
  subtotal: number;
}

interface Client {
  id: number;
  nombre: string;
  email?: string;
  telefono?: string;
}

interface POSProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
    };
  };
  products: Product[];
  clients: Client[];
  categories: string[];
}

export default function POS({ auth, products, clients, categories }: POSProps) {
  const [cart, setCart] = useState<CartItem[]>([]);
  const [selectedClient, setSelectedClient] = useState<Client | null>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [paymentMethod, setPaymentMethod] = useState<'cash' | 'card'>('cash');
  const [showClientModal, setShowClientModal] = useState(false);
  const [clientSearch, setClientSearch] = useState('');
  const [amountPaid, setAmountPaid] = useState<string>('');

  // Filtrar productos
  const filteredProducts = products.filter(product => {
    const matchesSearch = product.nombre.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = selectedCategory === 'all' || product.categoria === selectedCategory;
    return matchesSearch && matchesCategory && product.stock > 0;
  });

  // Calcular totales del carrito
  const cartTotal = cart.reduce((total, item) => total + item.subtotal, 0);
  const cartItemsCount = cart.reduce((total, item) => total + item.cantidad, 0);

  // Agregar producto al carrito
  const addToCart = (product: Product) => {
    const existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
      if (existingItem.cantidad < product.stock) {
        setCart(cart.map(item =>
          item.id === product.id
            ? { ...item, cantidad: item.cantidad + 1, subtotal: (item.cantidad + 1) * item.precio }
            : item
        ));
      }
    } else {
      const newItem: CartItem = {
        id: product.id,
        nombre: product.nombre,
        precio: product.precio,
        cantidad: 1,
        subtotal: product.precio
      };
      setCart([...cart, newItem]);
    }
  };

  // Actualizar cantidad en carrito
  const updateCartQuantity = (itemId: number, newQuantity: number) => {
    if (newQuantity <= 0) {
      removeFromCart(itemId);
      return;
    }

    const product = products.find(p => p.id === itemId);
    if (product && newQuantity <= product.stock) {
      setCart(cart.map(item =>
        item.id === itemId
          ? { ...item, cantidad: newQuantity, subtotal: newQuantity * item.precio }
          : item
      ));
    }
  };

  // Remover del carrito
  const removeFromCart = (itemId: number) => {
    setCart(cart.filter(item => item.id !== itemId));
  };

  // Limpiar carrito
  const clearCart = () => {
    setCart([]);
    setSelectedClient(null);
    setAmountPaid('');
  };

  // Procesar venta
  const processSale = () => {
    if (cart.length === 0) return;

    const saleData = {
      cliente_id: selectedClient?.id || null,
      items: JSON.stringify(cart),
      total: cartTotal.toString(),
      metodo_pago: paymentMethod,
      monto_pagado: (paymentMethod === 'cash' ? parseFloat(amountPaid) || cartTotal : cartTotal).toString()
    };

    router.post('/pos/process-sale', saleData, {
      onSuccess: () => {
        clearCart();
        // Mostrar mensaje de éxito
      },
      onError: (errors) => {
        console.error('Error processing sale:', errors);
      }
    });
  };

  const change = paymentMethod === 'cash' && amountPaid 
    ? parseFloat(amountPaid) - cartTotal 
    : 0;

  return (
    <AuthenticatedLayout
      header={
        <div className="flex justify-between items-center">
          <h2 className="font-semibold text-xl text-gray-800 leading-tight">
            Punto de Venta
          </h2>
          <div className="flex items-center gap-4">
            <div className="text-sm text-gray-600">
              Cajero: <span className="font-medium">{auth.user.name}</span>
            </div>
            <div className="text-sm text-gray-600">
              {new Date().toLocaleString()}
            </div>
          </div>
        </div>
      }
    >
      <Head title="Punto de Venta" />

      <div className="py-6">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {/* Productos */}
            <div className="lg:col-span-2 space-y-6">
              {/* Filtros */}
              <MetricCard
                title="Filtros de Productos"
                icon={<MagnifyingGlassIcon className="w-6 h-6" />}
                color="blue"
              >
                <div className="flex flex-col sm:flex-row gap-4">
                  <div className="flex-1 relative">
                    <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                    <input
                      type="text"
                      placeholder="Buscar productos..."
                      className="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                      value={searchTerm}
                      onChange={(e) => setSearchTerm(e.target.value)}
                    />
                  </div>
                  <select
                    className="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    value={selectedCategory}
                    onChange={(e) => setSelectedCategory(e.target.value)}
                  >
                    <option value="all">Todas las categorías</option>
                    {categories.map((category) => (
                      <option key={category} value={category}>
                        {category}
                      </option>
                    ))}
                  </select>
                </div>
              </MetricCard>

              {/* Grid de Productos */}
              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                {filteredProducts.map((product) => (
                  <div
                    key={product.id}
                    className="bg-white rounded-lg shadow border cursor-pointer hover:shadow-md transition-shadow"
                    onClick={() => addToCart(product)}
                  >
                    <div className="aspect-square bg-gray-100 rounded-t-lg flex items-center justify-center">
                      {product.imagen ? (
                        <img
                          src={product.imagen}
                          alt={product.nombre}
                          className="w-full h-full object-cover rounded-t-lg"
                        />
                      ) : (
                        <span className="text-4xl text-gray-400">📦</span>
                      )}
                    </div>
                    <div className="p-3">
                      <h3 className="font-medium text-sm text-gray-900 mb-1 line-clamp-2">
                        {product.nombre}
                      </h3>
                      <div className="flex items-center justify-between">
                        <span className="text-lg font-bold text-green-600">
                          ${product.precio.toLocaleString()}
                        </span>
                        <span className="text-xs text-gray-500">
                          Stock: {product.stock}
                        </span>
                      </div>
                    </div>
                  </div>
                ))}
              </div>

              {filteredProducts.length === 0 && (
                <div className="text-center py-12 bg-white rounded-lg shadow border">
                  <div className="text-gray-400 text-6xl mb-4">🔍</div>
                  <h3 className="text-lg font-medium text-gray-900 mb-2">
                    No se encontraron productos
                  </h3>
                  <p className="text-gray-500">
                    Intenta cambiar los filtros de búsqueda
                  </p>
                </div>
              )}
            </div>

            {/* Carrito y Checkout */}
            <div className="space-y-6">
              {/* Cliente */}
              <MetricCard
                title="Cliente"
                icon={<UserIcon className="w-6 h-6" />}
                color="green"
                action={
                  <ActionCard
                    icon={<UserIcon className="w-4 h-4" />}
                    title="Seleccionar"
                    onClick={() => setShowClientModal(true)}
                    variant="ghost"
                  />
                }
              >
                {selectedClient ? (
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="font-medium text-gray-900">{selectedClient.nombre}</p>
                      {selectedClient.telefono && (
                        <p className="text-sm text-gray-500">{selectedClient.telefono}</p>
                      )}
                    </div>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => setSelectedClient(null)}
                    >
                      <XMarkIcon className="w-4 h-4" />
                    </Button>
                  </div>
                ) : (
                  <p className="text-gray-500 text-center py-4">
                    Cliente general
                  </p>
                )}
              </MetricCard>

              {/* Carrito */}
              <Card>
                <CardHeader>
                  <div className="flex items-center justify-between">
                    <h3 className="text-lg font-medium text-gray-900">
                      Carrito ({cartItemsCount})
                    </h3>
                    {cart.length > 0 && (
                      <Button
                        variant="ghost"
                        size="sm"
                        onClick={clearCart}
                        className="text-red-600 hover:text-red-700"
                      >
                        Limpiar
                      </Button>
                    )}
                  </div>
                </CardHeader>
                <CardBody>
                  <div className="space-y-3">
                    {cart.map((item) => (
                      <div key={item.id} className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <div className="flex-1">
                          <p className="font-medium text-sm text-gray-900">{item.nombre}</p>
                          <p className="text-sm text-gray-500">
                            ${item.precio.toLocaleString()} c/u
                          </p>
                        </div>
                        <div className="flex items-center gap-2">
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => updateCartQuantity(item.id, item.cantidad - 1)}
                          >
                            <MinusIcon className="w-4 h-4" />
                          </Button>
                          <span className="w-8 text-center font-medium">
                            {item.cantidad}
                          </span>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => updateCartQuantity(item.id, item.cantidad + 1)}
                          >
                            <PlusIcon className="w-4 h-4" />
                          </Button>
                        </div>
                        <div className="text-right">
                          <p className="font-medium text-gray-900">
                            ${item.subtotal.toLocaleString()}
                          </p>
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => removeFromCart(item.id)}
                            className="text-red-600 hover:text-red-700"
                          >
                            <XMarkIcon className="w-4 h-4" />
                          </Button>
                        </div>
                      </div>
                    ))}

                    {cart.length === 0 && (
                      <div className="text-center py-8 text-gray-500">
                        <ShoppingCartIcon className="w-12 h-12 mx-auto mb-3 text-gray-300" />
                        <p>El carrito está vacío</p>
                        <p className="text-sm">Selecciona productos para comenzar</p>
                      </div>
                    )}
                  </div>
                </CardBody>
              </Card>

              {/* Total y Pago */}
              {cart.length > 0 && (
                <Card>
                  <CardHeader>
                    <h3 className="text-lg font-medium text-gray-900">Pago</h3>
                  </CardHeader>
                  <CardBody>
                    <div className="space-y-4">
                      {/* Total */}
                      <div className="border-t border-gray-200 pt-4">
                        <div className="flex justify-between text-xl font-bold text-gray-900">
                          <span>Total</span>
                          <span>${cartTotal.toLocaleString()}</span>
                        </div>
                      </div>

                      {/* Método de pago */}
                      <div className="space-y-2">
                        <label className="block text-sm font-medium text-gray-700">
                          Método de pago
                        </label>
                        <div className="grid grid-cols-2 gap-2">
                          <button
                            type="button"
                            className={clsx(
                              "flex items-center justify-center px-3 py-2 border rounded-md text-sm font-medium",
                              paymentMethod === 'cash'
                                ? "border-blue-500 bg-blue-50 text-blue-700"
                                : "border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
                            )}
                            onClick={() => setPaymentMethod('cash')}
                          >
                            <BanknotesIcon className="w-4 h-4 mr-2" />
                            Efectivo
                          </button>
                          <button
                            type="button"
                            className={clsx(
                              "flex items-center justify-center px-3 py-2 border rounded-md text-sm font-medium",
                              paymentMethod === 'card'
                                ? "border-blue-500 bg-blue-50 text-blue-700"
                                : "border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
                            )}
                            onClick={() => setPaymentMethod('card')}
                          >
                            <CreditCardIcon className="w-4 h-4 mr-2" />
                            Tarjeta
                          </button>
                        </div>
                      </div>

                      {/* Monto pagado (solo efectivo) */}
                      {paymentMethod === 'cash' && (
                        <div className="space-y-2">
                          <label className="block text-sm font-medium text-gray-700">
                            Monto pagado
                          </label>
                          <input
                            type="number"
                            step="0.01"
                            min={cartTotal}
                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            value={amountPaid}
                            onChange={(e) => setAmountPaid(e.target.value)}
                            placeholder={cartTotal.toString()}
                          />
                          {change > 0 && (
                            <div className="text-sm">
                              <span className="text-gray-600">Cambio: </span>
                              <span className="font-medium text-green-600">
                                ${change.toLocaleString()}
                              </span>
                            </div>
                          )}
                        </div>
                      )}

                      {/* Botón de pago */}
                      <Button
                        variant="primary"
                        size="lg"
                        className="w-full"
                        onClick={processSale}
                        disabled={
                          paymentMethod === 'cash' && 
                          amountPaid !== '' && 
                          parseFloat(amountPaid) < cartTotal
                        }
                      >
                        <CalculatorIcon className="w-5 h-5 mr-2" />
                        Procesar Venta
                      </Button>

                      <Button
                        variant="secondary"
                        size="lg"
                        className="w-full"
                        onClick={() => {/* Función para imprimir ticket */}}
                      >
                        <PrinterIcon className="w-5 h-5 mr-2" />
                        Vista Previa Ticket
                      </Button>
                    </div>
                  </CardBody>
                </Card>
              )}
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
