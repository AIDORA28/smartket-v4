import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { CompanyAnalyticsIndexProps } from '@/Types/core';
import {
  ChartBarIcon,
  UsersIcon,
  ShoppingCartIcon,
  CurrencyDollarIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  BuildingOfficeIcon,
  StarIcon,
  ArrowDownTrayIcon,
  ArrowPathIcon
} from '@heroicons/react/24/outline';
import {
  LineChart,
  Line,
  AreaChart,
  Area,
  BarChart,
  Bar,
  PieChart,
  Pie,
  Cell,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  ResponsiveContainer
} from 'recharts';

const CompanyAnalyticsIndex: React.FC = () => {
  const props = usePage().props as any;
  const { empresa, analytics, can } = props;

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('es-PE', {
      style: 'currency',
      currency: 'PEN'
    }).format(amount);
  };

  const formatNumber = (num: number) => {
    return new Intl.NumberFormat('es-PE').format(num);
  };

  const getTrendIcon = (percentage: number) => {
    return percentage >= 0 ? (
      <ArrowTrendingUpIcon className="w-5 h-5 text-green-500" />
    ) : (
      <ArrowTrendingDownIcon className="w-5 h-5 text-red-500" />
    );
  };

  const getTrendColor = (percentage: number) => {
    return percentage >= 0 ? 'text-green-600' : 'text-red-600';
  };

  return (
    <AuthenticatedLayout>
      <Head title="Analytics de Empresa" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          {/* Header */}
          <div className="mb-8">
            <div className="flex items-center justify-between">
              <div>
                <h1 className="text-3xl font-bold text-gray-900">
                  Analytics de Empresa
                </h1>
                <p className="mt-2 text-sm text-gray-600">
                  Métricas y análisis de rendimiento de {empresa.nombre}
                </p>
              </div>
              <div className="flex items-center space-x-3">
                {can.export_reports && (
                  <button className="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Exportar Reporte
                  </button>
                )}
                <button
                  onClick={() => window.location.reload()}
                  className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                >
                  Actualizar Datos
                </button>
              </div>
            </div>
          </div>

          {/* Main KPIs */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {/* Ventas del Mes */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <CurrencyDollarIcon className="w-8 h-8 text-green-600" />
                </div>
                <div className="ml-4 flex-1">
                  <p className="text-sm font-medium text-gray-500">Ventas del Mes</p>
                  <p className="text-2xl font-semibold text-gray-900">
                    {formatCurrency(analytics?.total_ventas_mes || 0)}
                  </p>
                  <div className="flex items-center mt-2">
                    {getTrendIcon(analytics?.crecimiento_mensual || 0)}
                    <span className={`ml-1 text-sm font-medium ${getTrendColor(analytics?.crecimiento_mensual || 0)}`}>
                      {Math.abs(analytics?.crecimiento_mensual || 0).toFixed(1)}%
                    </span>
                    <span className="ml-1 text-sm text-gray-500">vs mes anterior</span>
                  </div>
                </div>
              </div>
            </div>

            {/* Total Clientes */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <UsersIcon className="w-8 h-8 text-blue-600" />
                </div>
                <div className="ml-4 flex-1">
                  <p className="text-sm font-medium text-gray-500">Total Clientes</p>
                  <p className="text-2xl font-semibold text-gray-900">
                    {formatNumber(analytics?.total_clientes || 0)}
                  </p>
                  <p className="text-sm text-gray-500 mt-2">
                    {formatNumber(analytics?.clientes_activos_mes || 0)} activos este mes
                  </p>
                </div>
              </div>
            </div>

            {/* Total Productos */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <ShoppingCartIcon className="w-8 h-8 text-purple-600" />
                </div>
                <div className="ml-4 flex-1">
                  <p className="text-sm font-medium text-gray-500">Total Productos</p>
                  <p className="text-2xl font-semibold text-gray-900">
                    {formatNumber(analytics?.total_productos || 0)}
                  </p>
                  <p className="text-sm text-gray-500 mt-2">
                    {formatNumber(analytics?.productos_vendidos_mes || 0)} vendidos este mes
                  </p>
                </div>
              </div>
            </div>

            {/* Margen Promedio */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <ChartBarIcon className="w-8 h-8 text-orange-600" />
                </div>
                <div className="ml-4 flex-1">
                  <p className="text-sm font-medium text-gray-500">Margen Promedio</p>
                  <p className="text-2xl font-semibold text-gray-900">
                    {(analytics?.margen_promedio || 0).toFixed(1)}%
                  </p>
                  <p className="text-sm text-gray-500 mt-2">
                    {analytics?.total_sucursales || 0} sucursales
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* Secondary Metrics */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            {/* Productos Top */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 className="text-lg font-medium text-gray-900 mb-4">
                Productos Más Vendidos
              </h3>
              {analytics?.productos_mas_vendidos && analytics.productos_mas_vendidos.length > 0 ? (
                <div className="space-y-3">
                  {analytics.productos_mas_vendidos.slice(0, 5).map((producto: any, index: number) => (
                    <div key={producto.id} className="flex items-center justify-between">
                      <div className="flex items-center">
                        <div className="flex-shrink-0">
                          <span className="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-800 text-xs font-medium">
                            {index + 1}
                          </span>
                        </div>
                        <div className="ml-3">
                          <p className="text-sm font-medium text-gray-900">{producto.nombre}</p>
                          <p className="text-sm text-gray-500">{producto.cantidad_vendida} unidades</p>
                        </div>
                      </div>
                      <div className="text-right">
                        <p className="text-sm font-medium text-gray-900">
                          {formatCurrency(producto.ingresos)}
                        </p>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-sm text-gray-500 text-center py-4">
                  No hay datos de productos disponibles
                </p>
              )}
            </div>

            {/* Categorías Top */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 className="text-lg font-medium text-gray-900 mb-4">
                Categorías Más Vendidas
              </h3>
              {analytics?.categorias_mas_vendidas && analytics.categorias_mas_vendidas.length > 0 ? (
                <div className="space-y-3">
                  {analytics.categorias_mas_vendidas.slice(0, 5).map((categoria: any, index: number) => (
                    <div key={categoria.id} className="flex items-center justify-between">
                      <div className="flex items-center">
                        <div className="flex-shrink-0">
                          <span className="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-800 text-xs font-medium">
                            {index + 1}
                          </span>
                        </div>
                        <div className="ml-3">
                          <p className="text-sm font-medium text-gray-900">{categoria.nombre}</p>
                          <p className="text-sm text-gray-500">{categoria.cantidad_vendida} productos</p>
                        </div>
                      </div>
                      <div className="text-right">
                        <p className="text-sm font-medium text-gray-900">
                          {formatCurrency(categoria.ingresos)}
                        </p>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-sm text-gray-500 text-center py-4">
                  No hay datos de categorías disponibles
                </p>
              )}
            </div>
          </div>

          {/* Customer Satisfaction & Reviews */}
          {analytics?.rating_promedio && (
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
              <h3 className="text-lg font-medium text-gray-900 mb-4">
                Satisfacción del Cliente
              </h3>
              <div className="flex items-center space-x-8">
                <div className="flex items-center">
                  <StarIcon className="w-8 h-8 text-yellow-400 fill-current" />
                  <div className="ml-2">
                    <p className="text-2xl font-semibold text-gray-900">
                      {analytics.rating_promedio.toFixed(1)}
                    </p>
                    <p className="text-sm text-gray-500">Rating promedio</p>
                  </div>
                </div>
                <div>
                  <p className="text-lg font-medium text-gray-900">
                    {formatNumber(analytics.total_reviews)}
                  </p>
                  <p className="text-sm text-gray-500">Total reviews</p>
                </div>
              </div>
            </div>
          )}

          {/* Last Update Info */}
          <div className="bg-gray-50 rounded-lg p-4 text-center">
            <p className="text-sm text-gray-600">
              Última actualización: {' '}
              {analytics?.ultima_actualizacion 
                ? new Date(analytics.ultima_actualizacion).toLocaleString('es-PE')
                : 'No disponible'
              }
            </p>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
};

export default CompanyAnalyticsIndex;
