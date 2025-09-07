import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { clsx } from 'clsx';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout';
import { Button } from '../Components/ui/Button';
import { Card, CardHeader, CardBody } from '../Components/ui/Card';
import { StatsCard } from '../Components/ui/StatsCard';
import {
  ChartBarIcon,
  ArrowPathIcon,
  CloudArrowDownIcon,
  CalendarIcon,
  CurrencyDollarIcon,
  ShoppingCartIcon,
  UserGroupIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon
} from '@heroicons/react/24/outline';

interface ReportData {
  ventas: {
    total: number;
    cantidad: number;
    promedio: number;
    variacion: number;
  };
  productos: {
    mas_vendidos: Array<{
      id: number;
      nombre: string;
      cantidad: number;
      total: number;
    }>;
    menos_vendidos: Array<{
      id: number;
      nombre: string;
      cantidad: number;
    }>;
  };
  clientes: {
    nuevos: number;
    recurrentes: number;
    top_compradores: Array<{
      id: number;
      nombre: string;
      total: number;
      compras: number;
    }>;
  };
  metodos_pago: {
    efectivo: { cantidad: number; total: number };
    tarjeta: { cantidad: number; total: number };
  };
  resumen_diario: Array<{
    fecha: string;
    ventas: number;
    cantidad: number;
  }>;
}

interface ReportsPageProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
    };
  };
  reportData: ReportData;
  filters: {
    fecha_inicio: string;
    fecha_fin: string;
  };
}

export default function Reports({ auth, reportData, filters }: ReportsPageProps) {
  const [startDate, setStartDate] = useState(filters.fecha_inicio);
  const [endDate, setEndDate] = useState(filters.fecha_fin);
  const [loading, setLoading] = useState(false);

  const updateFilters = () => {
    setLoading(true);
    router.get('/reports', {
      fecha_inicio: startDate,
      fecha_fin: endDate
    }, {
      preserveState: true,
      onFinish: () => setLoading(false)
    });
  };

  const exportReport = (type: string) => {
    router.get(`/reports/export/${type}`, {
      fecha_inicio: startDate,
      fecha_fin: endDate
    });
  };

  const formatCurrency = (amount: number) => `$${amount.toLocaleString()}`;
  const formatPercent = (value: number) => `${value > 0 ? '+' : ''}${value.toFixed(1)}%`;

  return (
    <AuthenticatedLayout
      header={
        <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <div>
            <h2 className="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
              📊 Reportes y Analytics
            </h2>
            <p className="text-gray-600 mt-1">
              Dashboard ejecutivo y suite de reportes visuales
            </p>
          </div>
          
          <div className="flex flex-col sm:flex-row gap-3">
            {/* Filtros de fecha */}
            <div className="flex gap-2">
              <input
                type="date"
                value={startDate}
                onChange={(e) => setStartDate(e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              <input
                type="date"
                value={endDate}
                onChange={(e) => setEndDate(e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            
            {/* Botón de actualizar */}
            <Button
              variant="primary"
              size="sm"
              onClick={updateFilters}
              disabled={loading}
            >
              {loading ? (
                <ArrowPathIcon className="w-4 h-4 mr-2 animate-spin" />
              ) : (
                <ArrowPathIcon className="w-4 h-4 mr-2" />
              )}
              {loading ? 'Actualizando...' : 'Actualizar'}
            </Button>
          </div>
        </div>
      }
    >
      <Head title="Reportes" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
          
          {/* KPIs Principales */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatsCard
              title="Ventas Totales"
              value={formatCurrency(reportData.ventas.total)}
              icon="money"
              trend={reportData.ventas.variacion !== 0 ? {
                value: Math.abs(reportData.ventas.variacion),
                label: `${formatPercent(reportData.ventas.variacion)} vs período anterior`,
                direction: reportData.ventas.variacion > 0 ? "up" : "down"
              } : undefined}
              color="green"
            />
            
            <StatsCard
              title="Número de Ventas"
              value={reportData.ventas.cantidad}
              icon="cart"
              color="blue"
            />
            
            <StatsCard
              title="Ticket Promedio"
              value={formatCurrency(reportData.ventas.promedio)}
              icon="money"
              color="purple"
            />
            
            <StatsCard
              title="Clientes Nuevos"
              value={reportData.clientes.nuevos}
              icon="users"
              color="green"
            />
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {/* Productos Más Vendidos */}
            <Card>
              <CardHeader>
                <div className="flex items-center justify-between">
                  <h3 className="text-lg font-medium text-gray-900">Productos Más Vendidos</h3>
                  <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => exportReport('productos-mas-vendidos')}
                  >
                    <CloudArrowDownIcon className="w-4 h-4" />
                  </Button>
                </div>
              </CardHeader>
              <CardBody>
                <div className="space-y-4">
                  {reportData.productos.mas_vendidos.map((product, index) => (
                    <div key={product.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                      <div className="flex items-center">
                        <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                          <span className="text-blue-600 font-bold text-sm">#{index + 1}</span>
                        </div>
                        <div>
                          <p className="font-medium text-gray-900">{product.nombre}</p>
                          <p className="text-sm text-gray-500">{product.cantidad} unidades vendidas</p>
                        </div>
                      </div>
                      <div className="text-right">
                        <p className="font-semibold text-green-600">{formatCurrency(product.total)}</p>
                      </div>
                    </div>
                  ))}
                  
                  {reportData.productos.mas_vendidos.length === 0 && (
                    <div className="text-center py-8 text-gray-500">
                      <p>No hay datos de productos vendidos en este período</p>
                    </div>
                  )}
                </div>
              </CardBody>
            </Card>

            {/* Top Compradores */}
            <Card>
              <CardHeader>
                <div className="flex items-center justify-between">
                  <h3 className="text-lg font-medium text-gray-900">Top Compradores</h3>
                  <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => exportReport('top-compradores')}
                  >
                    <CloudArrowDownIcon className="w-4 h-4" />
                  </Button>
                </div>
              </CardHeader>
              <CardBody>
                <div className="space-y-4">
                  {reportData.clientes.top_compradores.map((client, index) => (
                    <div key={client.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                      <div className="flex items-center">
                        <div className="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                          <span className="text-purple-600 font-bold text-sm">#{index + 1}</span>
                        </div>
                        <div>
                          <p className="font-medium text-gray-900">{client.nombre}</p>
                          <p className="text-sm text-gray-500">{client.compras} compras realizadas</p>
                        </div>
                      </div>
                      <div className="text-right">
                        <p className="font-semibold text-purple-600">{formatCurrency(client.total)}</p>
                      </div>
                    </div>
                  ))}
                  
                  {reportData.clientes.top_compradores.length === 0 && (
                    <div className="text-center py-8 text-gray-500">
                      <p>No hay datos de clientes en este período</p>
                    </div>
                  )}
                </div>
              </CardBody>
            </Card>
          </div>

          {/* Métodos de Pago */}
          <Card>
            <CardHeader>
              <h3 className="text-lg font-medium text-gray-900">Métodos de Pago</h3>
            </CardHeader>
            <CardBody>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="bg-green-50 p-6 rounded-lg">
                  <div className="flex items-center justify-between mb-4">
                    <h4 className="font-medium text-green-900">Efectivo</h4>
                    <div className="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                      <CurrencyDollarIcon className="w-6 h-6 text-green-600" />
                    </div>
                  </div>
                  <div className="space-y-2">
                    <div className="flex justify-between">
                      <span className="text-green-700">Total:</span>
                      <span className="font-bold text-green-900">
                        {formatCurrency(reportData.metodos_pago.efectivo.total)}
                      </span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-green-700">Transacciones:</span>
                      <span className="font-semibold text-green-800">
                        {reportData.metodos_pago.efectivo.cantidad}
                      </span>
                    </div>
                  </div>
                </div>

                <div className="bg-blue-50 p-6 rounded-lg">
                  <div className="flex items-center justify-between mb-4">
                    <h4 className="font-medium text-blue-900">Tarjeta</h4>
                    <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                      <ChartBarIcon className="w-6 h-6 text-blue-600" />
                    </div>
                  </div>
                  <div className="space-y-2">
                    <div className="flex justify-between">
                      <span className="text-blue-700">Total:</span>
                      <span className="font-bold text-blue-900">
                        {formatCurrency(reportData.metodos_pago.tarjeta.total)}
                      </span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-blue-700">Transacciones:</span>
                      <span className="font-semibold text-blue-800">
                        {reportData.metodos_pago.tarjeta.cantidad}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </CardBody>
          </Card>

          {/* Resumen Diario */}
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <h3 className="text-lg font-medium text-gray-900">Resumen Diario</h3>
                <div className="flex gap-2">
                  <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => exportReport('resumen-diario')}
                  >
                    <CloudArrowDownIcon className="w-4 h-4 mr-2" />
                    Excel
                  </Button>
                  <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => exportReport('resumen-diario-pdf')}
                  >
                    <CloudArrowDownIcon className="w-4 h-4 mr-2" />
                    PDF
                  </Button>
                </div>
              </div>
            </CardHeader>
            <CardBody>
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <CalendarIcon className="w-4 h-4 inline mr-2" />
                        Fecha
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <ShoppingCartIcon className="w-4 h-4 inline mr-2" />
                        Ventas
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <CurrencyDollarIcon className="w-4 h-4 inline mr-2" />
                        Total
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tendencia
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {reportData.resumen_diario.map((day, index) => {
                      const prevDay = reportData.resumen_diario[index - 1];
                      const trend = prevDay ? day.ventas - prevDay.ventas : 0;
                      
                      return (
                        <tr key={day.fecha} className="hover:bg-gray-50">
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {new Date(day.fecha).toLocaleDateString('es-ES', { 
                              weekday: 'short',
                              day: '2-digit',
                              month: '2-digit' 
                            })}
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {day.cantidad} ventas
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {formatCurrency(day.ventas)}
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-sm">
                            {trend !== 0 && (
                              <div className={clsx(
                                "flex items-center",
                                trend > 0 ? "text-green-600" : "text-red-600"
                              )}>
                                {trend > 0 ? (
                                  <ArrowTrendingUpIcon className="w-4 h-4 mr-1" />
                                ) : (
                                  <ArrowTrendingDownIcon className="w-4 h-4 mr-1" />
                                )}
                                {formatCurrency(Math.abs(trend))}
                              </div>
                            )}
                          </td>
                        </tr>
                      );
                    })}
                  </tbody>
                </table>
                
                {reportData.resumen_diario.length === 0 && (
                  <div className="text-center py-12">
                    <div className="text-gray-400 text-6xl mb-4">📊</div>
                    <h3 className="text-lg font-medium text-gray-900 mb-2">
                      No hay datos disponibles
                    </h3>
                    <p className="text-gray-500">
                      Selecciona un rango de fechas diferente para ver los reportes
                    </p>
                  </div>
                )}
              </div>
            </CardBody>
          </Card>

          {/* Exportar Todo */}
          <Card>
            <CardHeader>
              <h3 className="text-lg font-medium text-gray-900">Exportar Reportes Completos</h3>
            </CardHeader>
            <CardBody>
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <Button
                  variant="secondary"
                  onClick={() => exportReport('completo-excel')}
                  className="justify-center"
                >
                  <CloudArrowDownIcon className="w-4 h-4 mr-2" />
                  Reporte Excel
                </Button>
                
                <Button
                  variant="secondary"
                  onClick={() => exportReport('completo-pdf')}
                  className="justify-center"
                >
                  <CloudArrowDownIcon className="w-4 h-4 mr-2" />
                  Reporte PDF
                </Button>
                
                <Button
                  variant="secondary"
                  onClick={() => exportReport('productos-stock')}
                  className="justify-center"
                >
                  <CloudArrowDownIcon className="w-4 h-4 mr-2" />
                  Stock de Productos
                </Button>
                
                <Button
                  variant="secondary"
                  onClick={() => exportReport('clientes-credito')}
                  className="justify-center"
                >
                  <CloudArrowDownIcon className="w-4 h-4 mr-2" />
                  Créditos Clientes
                </Button>
              </div>
            </CardBody>
          </Card>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
