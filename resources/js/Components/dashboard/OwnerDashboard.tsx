import React from 'react';
import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
  CurrencyDollarIcon,
  UsersIcon,
  ShoppingCartIcon,
  ChartBarIcon,
  Cog6ToothIcon,
  BuildingOffice2Icon,
  PresentationChartLineIcon,
  StarIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon
} from '@heroicons/react/24/outline';

// Importar componentes modulares
import MetricCard from '../core/shared/MetricCard';
import ActionCard from '../core/shared/ActionCard';

interface OwnerDashboardProps {
  auth: {
    user: {
      name: string;
      rol_principal: string;
    };
  };
  empresa: {
    nombre: string;
    plan?: {
      nombre: string;
    };
  };
  sucursal: {
    nombre: string;
  };
  stats: {
    ventasHoy: number;
    productosStock: number;
    clientesActivos: number;
    facturacionMensual: number;
  };
  sucursales?: Array<{
    id: number;
    nombre: string;
    ventas_hoy?: number;
    status?: 'active' | 'warning' | 'error';
  }>;
  alertas?: {
    stock_bajo: number;
    ventas_objetivo: boolean;
    plan_limite: boolean;
  };
}

const OwnerDashboard: React.FC<OwnerDashboardProps> = ({
  auth,
  empresa,
  sucursal,
  stats,
  sucursales = [],
  alertas = { stock_bajo: 0, ventas_objetivo: true, plan_limite: false }
}) => {

  const formatCurrency = (amount: number = 0) => {
    return new Intl.NumberFormat('es-PE', {
      style: 'currency',
      currency: 'PEN',
      minimumFractionDigits: 0
    }).format(amount);
  };

  const safeStats = {
    ventasHoy: stats?.ventasHoy || 0,
    clientesActivos: stats?.clientesActivos || 0,
    productosStock: stats?.productosStock || 0,
    facturacionMensual: stats?.facturacionMensual || 0
  };

  return (
    <div className="space-y-8">
      {/* Header Bienvenida */}
      <div className="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-6 text-white">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold">
              👑 Bienvenido, {auth?.user?.name || 'Usuario'}
            </h1>
            <p className="text-blue-100 text-lg">
              Gestiona {empresa?.nombre || 'tu empresa'} desde aquí
            </p>
          </div>
          <div className="text-right">
            <div className="flex items-center space-x-2">
              <StarIcon className="w-5 h-5 text-yellow-300" />
              <span className="text-sm font-medium">
                {empresa?.plan?.nombre || 'Plan Básico'}
              </span>
            </div>
            <p className="text-blue-100 text-sm">
              📍 {sucursal?.nombre || 'Sucursal Principal'}
            </p>
          </div>
        </div>
      </div>

      {/* Alertas Importantes */}
      {(alertas.stock_bajo > 0 || alertas.plan_limite) && (
        <div className="bg-amber-50 border border-amber-200 rounded-xl p-4">
          <div className="flex items-start space-x-3">
            <ExclamationTriangleIcon className="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" />
            <div>
              <h3 className="font-medium text-amber-800">⚠️ Atención requerida</h3>
              <div className="mt-2 space-y-1 text-sm text-amber-700">
                {alertas.stock_bajo > 0 && (
                  <p>• {alertas.stock_bajo} productos con stock bajo</p>
                )}
                {alertas.plan_limite && (
                  <p>• Te estás acercando a los límites de tu plan</p>
                )}
              </div>
              <div className="mt-3 flex space-x-3">
                {alertas.stock_bajo > 0 && (
                  <Link
                    href="/inventario"
                    className="text-amber-800 font-medium hover:text-amber-900"
                  >
                    Ver inventario →
                  </Link>
                )}
                {alertas.plan_limite && (
                  <Link
                    href="/core/company/settings"
                    className="text-amber-800 font-medium hover:text-amber-900"
                  >
                    Gestionar plan →
                  </Link>
                )}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Métricas Principales */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <MetricCard
          title="💰 Ventas Hoy"
          value={formatCurrency(safeStats.ventasHoy)}
          emoji="💰"
          color="green"
          trend={{ value: 12, isPositive: true }}
        />
        
        <MetricCard
          title="👥 Clientes"
          value={safeStats.clientesActivos.toLocaleString()}
          emoji="👥"
          color="blue"
          subtitle="activos este mes"
        />
        
        <MetricCard
          title="📦 Productos"
          value={safeStats.productosStock.toLocaleString()}
          emoji="📦"
          color={alertas.stock_bajo > 0 ? "red" : "purple"}
          subtitle={alertas.stock_bajo > 0 ? `⚠️ ${alertas.stock_bajo} stock bajo` : "✅ Stock normal"}
        />
        
        <MetricCard
          title="📊 Este Mes"
          value={formatCurrency(safeStats.facturacionMensual)}
          emoji="📊"
          color="indigo"
          trend={{ value: 8, isPositive: true }}
        />
      </div>

      {/* Acciones Rápidas */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">
          🚀 Acciones Rápidas
        </h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <ActionCard
            title="Abrir Caja"
            description="Iniciar día de ventas"
            emoji="💳"
            href="/cajas"
            color="green"
          />
          
          <ActionCard
            title="Nueva Venta"
            description="Punto de venta"
            emoji="🛒"
            href="/pos"
            color="blue"
          />
          
          <ActionCard
            title="Ver Stock"
            description="Gestionar inventario"
            emoji="📦"
            href="/inventario"
            color="purple"
          />
          
          <ActionCard
            title="Configurar"
            description="Empresa y usuarios"
            emoji="⚙️"
            href="/core/company/settings"
            color="orange"
          />
        </div>
      </div>

      {/* Mis Sucursales */}
      {sucursales && sucursales.length > 0 && (
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold text-gray-900">
              🏪 Mis Sucursales
            </h2>
            <Link
              href="/core/branches"
              className="text-sm text-blue-600 hover:text-blue-800 font-medium"
            >
              Gestionar todas →
            </Link>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {sucursales.slice(0, 3).map((sucursal) => (
              <div key={sucursal.id} className="p-4 border border-gray-200 rounded-lg">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="font-medium text-gray-900">{sucursal.nombre}</h3>
                  <div className={`w-3 h-3 rounded-full ${
                    sucursal.status === 'active' ? 'bg-green-400' :
                    sucursal.status === 'warning' ? 'bg-yellow-400' : 'bg-red-400'
                  }`}></div>
                </div>
                <p className="text-lg font-semibold text-gray-900">
                  {formatCurrency(sucursal.ventas_hoy || 0)}
                </p>
                <p className="text-sm text-gray-600">ventas hoy</p>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Plan y Límites */}
      <div className="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-6 border border-purple-200">
        <div className="flex items-center justify-between">
          <div>
            <h2 className="text-lg font-semibold text-gray-900 mb-2">
              ⭐ {empresa?.plan?.nombre || 'Plan Básico'}
            </h2>
            <p className="text-gray-600">
              Todo funcionando correctamente. Tu negocio está creciendo.
            </p>
          </div>
          <div className="text-right">
            <Link
              href="/core/company/settings"
              className="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors"
            >
              <StarIcon className="w-4 h-4 mr-2" />
              Administrar Plan
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
};

export default OwnerDashboard;
