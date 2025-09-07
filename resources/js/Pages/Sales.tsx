import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { clsx } from 'clsx';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout';
import { Button } from '../Components/ui/Button';
import { Card, CardHeader, CardBody } from '../Components/ui/Card';
import {
  MagnifyingGlassIcon,
  EyeIcon,
  PrinterIcon,
  XMarkIcon,
  CalendarIcon,
  CurrencyDollarIcon,
  ShoppingCartIcon,
  UserIcon,
  CreditCardIcon,
  BanknotesIcon,
  AdjustmentsHorizontalIcon,
  CloudArrowDownIcon
} from '@heroicons/react/24/outline';

interface SaleItem {
  id: number;
  producto_id: number;
  producto_nombre: string;
  cantidad: number;
  precio: number;
  subtotal: number;
}

interface Sale {
  id: number;
  numero_venta: string;
  cliente_id?: number;
  cliente_nombre?: string;
  total: number;
  metodo_pago: 'cash' | 'card';
  estado: 'completed' | 'pending' | 'cancelled';
  items: SaleItem[];
  created_at: string;
  cajero: string;
}

interface SalesPageProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
    };
  };
  sales: Sale[];
  stats: {
    total_ventas: number;
    total_cantidad: number;
    promedio_ticket: number;
    ventas_efectivo: number;
    ventas_tarjeta: number;
  };
  filters: {
    search?: string;
    date_from?: string;
    date_to?: string;
    payment_method?: string;
    status?: string;
  };
}

export default function Sales({ auth, sales, stats, filters }: SalesPageProps) {
  const [searchTerm, setSearchTerm] = useState(filters.search || '');
  const [dateFrom, setDateFrom] = useState(filters.date_from || '');
  const [dateTo, setDateTo] = useState(filters.date_to || '');
  const [paymentMethod, setPaymentMethod] = useState(filters.payment_method || 'all');
  const [status, setStatus] = useState(filters.status || 'all');
  const [selectedSale, setSelectedSale] = useState<Sale | null>(null);

  // Filtrar ventas
  const filteredSales = sales.filter(sale => {
    const matchesSearch = sale.numero_venta.includes(searchTerm) ||
                         sale.cliente_nombre?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         sale.cajero.toLowerCase().includes(searchTerm.toLowerCase());
                         
    const saleDate = new Date(sale.created_at);
    const matchesDateFrom = !dateFrom || saleDate >= new Date(dateFrom);
    const matchesDateTo = !dateTo || saleDate <= new Date(dateTo + ' 23:59:59');
    
    const matchesPayment = paymentMethod === 'all' || sale.metodo_pago === paymentMethod;
    const matchesStatus = status === 'all' || sale.estado === status;
    
    return matchesSearch && matchesDateFrom && matchesDateTo && matchesPayment && matchesStatus;
  });

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'completed':
        return 'bg-green-100 text-green-800';
      case 'pending':
        return 'bg-yellow-100 text-yellow-800';
      case 'cancelled':
        return 'bg-red-100 text-red-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  };

  const getStatusText = (status: string) => {
    switch (status) {
      case 'completed':
        return 'Completada';
      case 'pending':
        return 'Pendiente';
      case 'cancelled':
        return 'Cancelada';
      default:
        return status;
    }
  };

  const applyFilters = () => {
    router.get('/sales', {
      search: searchTerm,
      date_from: dateFrom,
      date_to: dateTo,
      payment_method: paymentMethod === 'all' ? undefined : paymentMethod,
      status: status === 'all' ? undefined : status
    }, {
      preserveState: true,
      only: ['sales']
    });
  };

  const printSale = (saleId: number) => {
    router.get(`/sales/${saleId}/print`);
  };

  const cancelSale = (saleId: number) => {
    if (confirm('¿Estás seguro de cancelar esta venta?')) {
      router.patch(`/sales/${saleId}/cancel`, {}, {
        onSuccess: () => {
          // Mostrar mensaje de éxito
        }
      });
    }
  };

  return (
    <AuthenticatedLayout
      header={
        <div className="flex justify-between items-center">
          <div>
            <h2 className="font-semibold text-xl text-gray-800 leading-tight">
              Historial de Ventas
            </h2>
            <p className="text-gray-600 text-sm mt-1">
              Registro completo de todas las transacciones
            </p>
          </div>
          <div className="flex gap-3">
            <Button variant="secondary" size="sm">
              <CloudArrowDownIcon className="w-4 h-4 mr-2" />
              Exportar
            </Button>
          </div>
        </div>
      }
    >
      <Head title="Ventas" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
          
          {/* Stats Cards */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div className="bg-blue-50 p-4 rounded-lg border">
              <div className="flex items-center justify-between">
                <div>
                  <div className="text-2xl font-bold text-blue-600">
                    ${stats.total_ventas.toLocaleString()}
                  </div>
                  <div className="text-sm text-blue-700">Total Ventas</div>
                </div>
                <CurrencyDollarIcon className="w-8 h-8 text-blue-500" />
              </div>
            </div>
            
            <div className="bg-green-50 p-4 rounded-lg border">
              <div className="flex items-center justify-between">
                <div>
                  <div className="text-2xl font-bold text-green-600">
                    {stats.total_cantidad}
                  </div>
                  <div className="text-sm text-green-700">Transacciones</div>
                </div>
                <ShoppingCartIcon className="w-8 h-8 text-green-500" />
              </div>
            </div>
            
            <div className="bg-purple-50 p-4 rounded-lg border">
              <div className="flex items-center justify-between">
                <div>
                  <div className="text-2xl font-bold text-purple-600">
                    ${stats.promedio_ticket.toLocaleString()}
                  </div>
                  <div className="text-sm text-purple-700">Ticket Promedio</div>
                </div>
                <CalendarIcon className="w-8 h-8 text-purple-500" />
              </div>
            </div>
            
            <div className="bg-green-50 p-4 rounded-lg border">
              <div className="flex items-center justify-between">
                <div>
                  <div className="text-2xl font-bold text-green-600">
                    ${stats.ventas_efectivo.toLocaleString()}
                  </div>
                  <div className="text-sm text-green-700">Efectivo</div>
                </div>
                <BanknotesIcon className="w-8 h-8 text-green-500" />
              </div>
            </div>
            
            <div className="bg-blue-50 p-4 rounded-lg border">
              <div className="flex items-center justify-between">
                <div>
                  <div className="text-2xl font-bold text-blue-600">
                    ${stats.ventas_tarjeta.toLocaleString()}
                  </div>
                  <div className="text-sm text-blue-700">Tarjeta</div>
                </div>
                <CreditCardIcon className="w-8 h-8 text-blue-500" />
              </div>
            </div>
          </div>

          {/* Filtros */}
          <Card>
            <CardHeader>
              <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h3 className="text-lg font-medium text-gray-900">Filtros y Búsqueda</h3>
                <div className="flex items-center gap-2">
                  <AdjustmentsHorizontalIcon className="w-5 h-5 text-gray-500" />
                  <span className="text-sm text-gray-500">
                    {filteredSales.length} de {sales.length} ventas
                  </span>
                </div>
              </div>
            </CardHeader>
            <CardBody>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                {/* Search */}
                <div className="relative">
                  <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                  <input
                    type="text"
                    placeholder="Buscar ventas..."
                    className="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                  />
                </div>

                {/* Date From */}
                <input
                  type="date"
                  className="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  value={dateFrom}
                  onChange={(e) => setDateFrom(e.target.value)}
                />

                {/* Date To */}
                <input
                  type="date"
                  className="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  value={dateTo}
                  onChange={(e) => setDateTo(e.target.value)}
                />

                {/* Payment Method Filter */}
                <select
                  className="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  value={paymentMethod}
                  onChange={(e) => setPaymentMethod(e.target.value)}
                >
                  <option value="all">Todos los pagos</option>
                  <option value="cash">Efectivo</option>
                  <option value="card">Tarjeta</option>
                </select>

                {/* Status Filter */}
                <select
                  className="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  value={status}
                  onChange={(e) => setStatus(e.target.value)}
                >
                  <option value="all">Todos los estados</option>
                  <option value="completed">Completadas</option>
                  <option value="pending">Pendientes</option>
                  <option value="cancelled">Canceladas</option>
                </select>
              </div>
              
              <Button
                variant="primary"
                onClick={applyFilters}
                className="w-full sm:w-auto"
              >
                Aplicar Filtros
              </Button>
            </CardBody>
          </Card>

          {/* Sales Table */}
          <Card>
            <CardHeader>
              <h3 className="text-lg font-medium text-gray-900">Lista de Ventas</h3>
            </CardHeader>
            <CardBody>
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        # Venta
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Cliente
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Pago
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fecha
                      </th>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {filteredSales.map((sale) => (
                      <tr key={sale.id} className="hover:bg-gray-50">
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm font-medium text-blue-600">
                            {sale.numero_venta}
                          </div>
                          <div className="text-xs text-gray-500">
                            {sale.items.length} productos
                          </div>
                        </td>
                        
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="flex items-center">
                            <UserIcon className="w-4 h-4 text-gray-400 mr-2" />
                            <div>
                              <div className="text-sm text-gray-900">
                                {sale.cliente_nombre || 'Cliente General'}
                              </div>
                              <div className="text-xs text-gray-500">
                                Cajero: {sale.cajero}
                              </div>
                            </div>
                          </div>
                        </td>
                        
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-lg font-semibold text-gray-900">
                            ${sale.total.toLocaleString()}
                          </div>
                        </td>
                        
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="flex items-center">
                            {sale.metodo_pago === 'cash' ? (
                              <BanknotesIcon className="w-4 h-4 text-green-500 mr-2" />
                            ) : (
                              <CreditCardIcon className="w-4 h-4 text-blue-500 mr-2" />
                            )}
                            <span className="text-sm text-gray-900">
                              {sale.metodo_pago === 'cash' ? 'Efectivo' : 'Tarjeta'}
                            </span>
                          </div>
                        </td>
                        
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className={clsx(
                            "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium",
                            getStatusColor(sale.estado)
                          )}>
                            {getStatusText(sale.estado)}
                          </span>
                        </td>
                        
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-900">
                            {new Date(sale.created_at).toLocaleDateString()}
                          </div>
                          <div className="text-xs text-gray-500">
                            {new Date(sale.created_at).toLocaleTimeString()}
                          </div>
                        </td>
                        
                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                          <div className="flex items-center justify-end gap-2">
                            <Button
                              variant="ghost"
                              size="sm"
                              onClick={() => setSelectedSale(sale)}
                            >
                              <EyeIcon className="w-4 h-4" />
                            </Button>
                            <Button
                              variant="ghost"
                              size="sm"
                              onClick={() => printSale(sale.id)}
                            >
                              <PrinterIcon className="w-4 h-4" />
                            </Button>
                            {sale.estado === 'completed' && (
                              <Button
                                variant="ghost"
                                size="sm"
                                className="text-red-600 hover:text-red-700"
                                onClick={() => cancelSale(sale.id)}
                              >
                                <XMarkIcon className="w-4 h-4" />
                              </Button>
                            )}
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
                
                {filteredSales.length === 0 && (
                  <div className="text-center py-12">
                    <div className="text-gray-400 text-6xl mb-4">🛒</div>
                    <h3 className="text-lg font-medium text-gray-900 mb-2">
                      No se encontraron ventas
                    </h3>
                    <p className="text-gray-500 mb-6">
                      {searchTerm || dateFrom || dateTo || paymentMethod !== 'all' || status !== 'all'
                        ? "Intenta ajustar los filtros de búsqueda"
                        : "Aún no hay ventas registradas"
                      }
                    </p>
                  </div>
                )}
              </div>
            </CardBody>
          </Card>
        </div>
      </div>

      {/* Modal de detalle de venta */}
      {selectedSale && (
        <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
          <div className="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-lg font-medium text-gray-900">
                Detalle de Venta #{selectedSale.numero_venta}
              </h3>
              <button
                onClick={() => setSelectedSale(null)}
                className="text-gray-400 hover:text-gray-600"
              >
                <XMarkIcon className="w-6 h-6" />
              </button>
            </div>
            
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <p className="text-sm font-medium text-gray-500">Cliente</p>
                  <p className="text-sm text-gray-900">{selectedSale.cliente_nombre || 'Cliente General'}</p>
                </div>
                <div>
                  <p className="text-sm font-medium text-gray-500">Cajero</p>
                  <p className="text-sm text-gray-900">{selectedSale.cajero}</p>
                </div>
                <div>
                  <p className="text-sm font-medium text-gray-500">Método de Pago</p>
                  <p className="text-sm text-gray-900">
                    {selectedSale.metodo_pago === 'cash' ? 'Efectivo' : 'Tarjeta'}
                  </p>
                </div>
                <div>
                  <p className="text-sm font-medium text-gray-500">Estado</p>
                  <span className={clsx(
                    "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium",
                    getStatusColor(selectedSale.estado)
                  )}>
                    {getStatusText(selectedSale.estado)}
                  </span>
                </div>
              </div>
              
              <div>
                <p className="text-sm font-medium text-gray-500 mb-3">Productos</p>
                <div className="space-y-2">
                  {selectedSale.items.map((item) => (
                    <div key={item.id} className="flex justify-between items-center p-2 bg-gray-50 rounded">
                      <div>
                        <p className="text-sm font-medium text-gray-900">{item.producto_nombre}</p>
                        <p className="text-xs text-gray-500">
                          {item.cantidad} x ${item.precio.toLocaleString()}
                        </p>
                      </div>
                      <div className="text-sm font-medium text-gray-900">
                        ${item.subtotal.toLocaleString()}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
              
              <div className="border-t pt-4">
                <div className="flex justify-between items-center text-lg font-semibold">
                  <span>Total</span>
                  <span>${selectedSale.total.toLocaleString()}</span>
                </div>
              </div>
            </div>
            
            <div className="mt-6 flex justify-end gap-3">
              <Button
                variant="secondary"
                onClick={() => printSale(selectedSale.id)}
              >
                <PrinterIcon className="w-4 h-4 mr-2" />
                Imprimir
              </Button>
              <Button
                variant="primary"
                onClick={() => setSelectedSale(null)}
              >
                Cerrar
              </Button>
            </div>
          </div>
        </div>
      )}
    </AuthenticatedLayout>
  );
}
