import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import MetricCard from '../../../Components/core/shared/MetricCard';
import {
  ChartBarIcon,
  CurrencyDollarIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  CalendarIcon,
  ClockIcon,
  UserGroupIcon,
  ShoppingCartIcon,
  BanknotesIcon,
  CubeIcon,
  ArrowPathIcon,
  DocumentChartBarIcon
} from '@heroicons/react/24/outline';

interface AnalyticsProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
      rol_principal: string;
    };
  };
  empresa: {
    id: number;
    nombre: string;
  };
  sucursal: {
    id: number;
    nombre: string;
  };
  metricas: {
    ventas_hoy: number;
    ventas_ayer: number;
    ventas_semana: number;
    ventas_mes: number;
    ventas_mes_anterior: number;
    clientes_nuevos: number;
    clientes_activos: number;
    productos_vendidos: number;
    ticket_promedio: number;
    margen_promedio: number;
    crecimiento_semanal: number;
    crecimiento_mensual: number;
  };
  graficos: {
    ventas_por_dia: Array<{
      fecha: string;
      ventas: number;
      tickets: number;
    }>;
    productos_top: Array<{
      id: number;
      nombre: string;
      cantidad: number;
      ingresos: number;
    }>;
    categorias_top: Array<{
      nombre: string;
      ventas: number;
      porcentaje: number;
    }>;
    horas_pico: Array<{
      hora: string;
      ventas: number;
    }>;
  };
  comparacion: {
    periodo: string;
    ventas_actual: number;
    ventas_anterior: number;
    clientes_actual: number;
    clientes_anterior: number;
    productos_actual: number;
    productos_anterior: number;
  };
}

export default function Analytics({ 
  auth, 
  empresa, 
  sucursal, 
  metricas, 
  graficos, 
  comparacion 
}: AnalyticsProps) {

  const [selectedPeriod, setSelectedPeriod] = useState('week');
  const [selectedMetric, setSelectedMetric] = useState('ventas');

  const formatCurrency = (amount: number = 0) => {
    return new Intl.NumberFormat('es-PE', {
      style: 'currency',
      currency: 'PEN',
      minimumFractionDigits: 0
    }).format(amount);
  };

  const formatPercentage = (value: number) => {
    const sign = value >= 0 ? '+' : '';
    return `${sign}${value.toFixed(1)}%`;
  };

  const getGrowthColor = (value: number) => {
    if (value > 0) return 'green';
    if (value < 0) return 'red';
    return 'blue';
  };

  const periods = [
    { id: 'today', name: 'Hoy', icon: ClockIcon },
    { id: 'week', name: 'Esta Semana', icon: CalendarIcon },
    { id: 'month', name: 'Este Mes', icon: CalendarIcon },
    { id: 'quarter', name: 'Trimestre', icon: CalendarIcon },
  ];

  const getDateRange = (period: string) => {
    const today = new Date();
    switch (period) {
      case 'today':
        return today.toLocaleDateString('es-PE');
      case 'week':
        const startWeek = new Date(today.setDate(today.getDate() - today.getDay()));
        return `${startWeek.toLocaleDateString('es-PE')} - ${new Date().toLocaleDateString('es-PE')}`;
      case 'month':
        return new Date().toLocaleDateString('es-PE', { month: 'long', year: 'numeric' });
      case 'quarter':
        const quarter = Math.floor((new Date().getMonth() + 3) / 3);
        return `Q${quarter} ${new Date().getFullYear()}`;
      default:
        return '';
    }
  };

  return (
    <AuthenticatedLayout>
      <Head title="Analytics Empresarial - SmartKet" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          
          {/* Header */}
          <div className="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl p-6 text-white mb-8">
            <div className="flex items-center justify-between">
              <div>
                <h1 className="text-3xl font-bold">
                  📊 Analytics Empresarial
                </h1>
                <p className="text-purple-100 text-lg mt-2">
                  Insights inteligentes para {empresa.nombre} - {sucursal.nombre}
                </p>
              </div>
              <div className="text-right">
                <div className="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                  <p className="text-sm font-medium">📅 {getDateRange(selectedPeriod)}</p>
                  <p className="text-purple-100 text-xs">Actualizado en tiempo real</p>
                </div>
              </div>
            </div>
          </div>

          {/* Period Selector */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-8">
            <div className="flex items-center justify-between">
              <h2 className="text-lg font-semibold text-gray-900">
                🎯 Período de Análisis
              </h2>
              <div className="flex space-x-2">
                {periods.map((period) => {
                  const Icon = period.icon;
                  return (
                    <button
                      key={period.id}
                      onClick={() => setSelectedPeriod(period.id)}
                      className={`flex items-center px-4 py-2 rounded-lg font-medium transition-colors ${
                        selectedPeriod === period.id
                          ? 'bg-blue-600 text-white'
                          : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                      }`}
                    >
                      <Icon className="w-4 h-4 mr-2" />
                      {period.name}
                    </button>
                  );
                })}
              </div>
            </div>
          </div>

          {/* Key Metrics */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <MetricCard
              title="💰 Ventas Totales"
              value={formatCurrency(metricas.ventas_mes)}
              emoji="💰"
              color={getGrowthColor(metricas.crecimiento_mensual)}
              trend={{ 
                value: Math.abs(metricas.crecimiento_mensual), 
                isPositive: metricas.crecimiento_mensual >= 0 
              }}
              subtitle="vs mes anterior"
            />
            
            <MetricCard
              title="🎫 Ticket Promedio"
              value={formatCurrency(metricas.ticket_promedio)}
              emoji="🎫"
              color="blue"
              subtitle={`${metricas.productos_vendidos} productos`}
            />
            
            <MetricCard
              title="👥 Clientes Nuevos"
              value={metricas.clientes_nuevos.toString()}
              emoji="👥"
              color="green"
              subtitle="este mes"
            />
            
            <MetricCard
              title="📈 Margen Promedio"
              value={`${metricas.margen_promedio.toFixed(1)}%`}
              emoji="📈"
              color="purple"
              subtitle="rentabilidad"
            />
          </div>

          {/* Performance Comparison */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 className="text-lg font-semibold text-gray-900 mb-6">
              📊 Comparación de Rendimiento
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              
              {/* Ventas Comparison */}
              <div className="text-center">
                <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <CurrencyDollarIcon className="w-8 h-8 text-green-600" />
                </div>
                <h3 className="font-medium text-gray-900 mb-2">Ventas</h3>
                <div className="space-y-1">
                  <p className="text-2xl font-bold text-gray-900">
                    {formatCurrency(comparacion.ventas_actual)}
                  </p>
                  <p className="text-sm text-gray-600">vs {formatCurrency(comparacion.ventas_anterior)}</p>
                  <div className={`flex items-center justify-center text-sm ${
                    comparacion.ventas_actual >= comparacion.ventas_anterior ? 'text-green-600' : 'text-red-600'
                  }`}>
                    {comparacion.ventas_actual >= comparacion.ventas_anterior ? (
                      <ArrowTrendingUpIcon className="w-4 h-4 mr-1" />
                    ) : (
                      <ArrowTrendingDownIcon className="w-4 h-4 mr-1" />
                    )}
                    {formatPercentage(((comparacion.ventas_actual - comparacion.ventas_anterior) / comparacion.ventas_anterior) * 100)}
                  </div>
                </div>
              </div>

              {/* Clientes Comparison */}
              <div className="text-center">
                <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <UserGroupIcon className="w-8 h-8 text-blue-600" />
                </div>
                <h3 className="font-medium text-gray-900 mb-2">Clientes</h3>
                <div className="space-y-1">
                  <p className="text-2xl font-bold text-gray-900">
                    {comparacion.clientes_actual.toLocaleString()}
                  </p>
                  <p className="text-sm text-gray-600">vs {comparacion.clientes_anterior.toLocaleString()}</p>
                  <div className={`flex items-center justify-center text-sm ${
                    comparacion.clientes_actual >= comparacion.clientes_anterior ? 'text-green-600' : 'text-red-600'
                  }`}>
                    {comparacion.clientes_actual >= comparacion.clientes_anterior ? (
                      <ArrowTrendingUpIcon className="w-4 h-4 mr-1" />
                    ) : (
                      <ArrowTrendingDownIcon className="w-4 h-4 mr-1" />
                    )}
                    {formatPercentage(((comparacion.clientes_actual - comparacion.clientes_anterior) / comparacion.clientes_anterior) * 100)}
                  </div>
                </div>
              </div>

              {/* Productos Comparison */}
              <div className="text-center">
                <div className="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <CubeIcon className="w-8 h-8 text-purple-600" />
                </div>
                <h3 className="font-medium text-gray-900 mb-2">Productos</h3>
                <div className="space-y-1">
                  <p className="text-2xl font-bold text-gray-900">
                    {comparacion.productos_actual.toLocaleString()}
                  </p>
                  <p className="text-sm text-gray-600">vs {comparacion.productos_anterior.toLocaleString()}</p>
                  <div className={`flex items-center justify-center text-sm ${
                    comparacion.productos_actual >= comparacion.productos_anterior ? 'text-green-600' : 'text-red-600'
                  }`}>
                    {comparacion.productos_actual >= comparacion.productos_anterior ? (
                      <ArrowTrendingUpIcon className="w-4 h-4 mr-1" />
                    ) : (
                      <ArrowTrendingDownIcon className="w-4 h-4 mr-1" />
                    )}
                    {formatPercentage(((comparacion.productos_actual - comparacion.productos_anterior) / comparacion.productos_anterior) * 100)}
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Charts Section */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            {/* Sales Trend Chart */}
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div className="flex items-center justify-between mb-6">
                <h3 className="text-lg font-semibold text-gray-900">
                  📈 Tendencia de Ventas (7 días)
                </h3>
                <button className="text-blue-600 hover:text-blue-800 text-sm font-medium">
                  Ver detalles →
                </button>
              </div>
              
              {/* Simple Chart Visualization */}
              <div className="space-y-4">
                {graficos.ventas_por_dia.slice(0, 7).map((dia, index) => {
                  const maxVentas = Math.max(...graficos.ventas_por_dia.map(d => d.ventas));
                  const percentage = (dia.ventas / maxVentas) * 100;
                  
                  return (
                    <div key={index} className="flex items-center space-x-4">
                      <div className="w-16 text-sm text-gray-600">
                        {new Date(dia.fecha).toLocaleDateString('es-PE', { 
                          weekday: 'short', 
                          month: 'short', 
                          day: 'numeric' 
                        })}
                      </div>
                      <div className="flex-1">
                        <div className="bg-gray-200 rounded-full h-6 relative overflow-hidden">
                          <div 
                            className="bg-gradient-to-r from-blue-500 to-purple-500 h-6 rounded-full transition-all duration-500 flex items-center justify-end pr-2"
                            style={{ width: `${percentage}%` }}
                          >
                            <span className="text-white text-xs font-medium">
                              {formatCurrency(dia.ventas)}
                            </span>
                          </div>
                        </div>
                      </div>
                      <div className="w-12 text-right text-sm text-gray-600">
                        {dia.tickets}
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>

            {/* Top Products */}
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div className="flex items-center justify-between mb-6">
                <h3 className="text-lg font-semibold text-gray-900">
                  🏆 Productos Más Vendidos
                </h3>
                <Link
                  href="/products"
                  className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                >
                  Ver todos →
                </Link>
              </div>
              
              <div className="space-y-4">
                {graficos.productos_top.slice(0, 5).map((producto, index) => (
                  <div key={producto.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div className="flex items-center space-x-3">
                      <div className={`w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold ${
                        index === 0 ? 'bg-yellow-500' :
                        index === 1 ? 'bg-gray-400' :
                        index === 2 ? 'bg-amber-600' : 'bg-blue-500'
                      }`}>
                        {index + 1}
                      </div>
                      <div>
                        <p className="font-medium text-gray-900">{producto.nombre}</p>
                        <p className="text-sm text-gray-600">{producto.cantidad} unidades</p>
                      </div>
                    </div>
                    <div className="text-right">
                      <p className="font-semibold text-gray-900">
                        {formatCurrency(producto.ingresos)}
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Categories & Peak Hours */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            {/* Categories Performance */}
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <h3 className="text-lg font-semibold text-gray-900 mb-6">
                📂 Rendimiento por Categorías
              </h3>
              
              <div className="space-y-4">
                {graficos.categorias_top.map((categoria, index) => (
                  <div key={index} className="space-y-2">
                    <div className="flex items-center justify-between">
                      <span className="text-sm font-medium text-gray-900">{categoria.nombre}</span>
                      <span className="text-sm text-gray-600">{categoria.porcentaje}%</span>
                    </div>
                    <div className="bg-gray-200 rounded-full h-3">
                      <div 
                        className={`h-3 rounded-full ${
                          index === 0 ? 'bg-green-500' :
                          index === 1 ? 'bg-blue-500' :
                          index === 2 ? 'bg-purple-500' : 'bg-gray-400'
                        }`}
                        style={{ width: `${categoria.porcentaje}%` }}
                      ></div>
                    </div>
                    <div className="text-sm text-gray-600">
                      {formatCurrency(categoria.ventas)}
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Peak Hours */}
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <h3 className="text-lg font-semibold text-gray-900 mb-6">
                ⏰ Horas Pico de Ventas
              </h3>
              
              <div className="space-y-3">
                {graficos.horas_pico.map((hora, index) => {
                  const maxVentas = Math.max(...graficos.horas_pico.map(h => h.ventas));
                  const percentage = (hora.ventas / maxVentas) * 100;
                  
                  return (
                    <div key={index} className="flex items-center space-x-4">
                      <div className="w-16 text-sm font-medium text-gray-900">
                        {hora.hora}
                      </div>
                      <div className="flex-1">
                        <div className="bg-gray-200 rounded-full h-4">
                          <div 
                            className="bg-gradient-to-r from-orange-400 to-red-500 h-4 rounded-full transition-all duration-500"
                            style={{ width: `${percentage}%` }}
                          ></div>
                        </div>
                      </div>
                      <div className="w-20 text-right text-sm text-gray-600">
                        {formatCurrency(hora.ventas)}
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>
          </div>

          {/* Quick Actions */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">
              🚀 Acciones Rápidas
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <Link
                href="/reports/detailed"
                className="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-xl border border-blue-200 transition-colors group"
              >
                <div className="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                  <DocumentChartBarIcon className="w-6 h-6 text-white" />
                </div>
                <div>
                  <p className="font-medium text-gray-900 group-hover:text-blue-700">
                    Reporte Detallado
                  </p>
                  <p className="text-sm text-gray-600">Análisis completo</p>
                </div>
              </Link>

              <Link
                href="/reports/export"
                className="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-xl border border-green-200 transition-colors group"
              >
                <div className="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                  <ArrowPathIcon className="w-6 h-6 text-white" />
                </div>
                <div>
                  <p className="font-medium text-gray-900 group-hover:text-green-700">
                    Exportar Datos
                  </p>
                  <p className="text-sm text-gray-600">Excel / PDF</p>
                </div>
              </Link>

              <Link
                href="/dashboard"
                className="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-xl border border-purple-200 transition-colors group"
              >
                <div className="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center mr-3">
                  <ChartBarIcon className="w-6 h-6 text-white" />
                </div>
                <div>
                  <p className="font-medium text-gray-900 group-hover:text-purple-700">
                    Dashboard Principal
                  </p>
                  <p className="text-sm text-gray-600">Vista general</p>
                </div>
              </Link>

              <Link
                href="/core/company/settings"
                className="flex items-center p-4 bg-orange-50 hover:bg-orange-100 rounded-xl border border-orange-200 transition-colors group"
              >
                <div className="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center mr-3">
                  <CubeIcon className="w-6 h-6 text-white" />
                </div>
                <div>
                  <p className="font-medium text-gray-900 group-hover:text-orange-700">
                    Configuración
                  </p>
                  <p className="text-sm text-gray-600">Ajustes empresa</p>
                </div>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
