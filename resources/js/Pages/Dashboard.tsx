import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
  ChartBarIcon,
  CurrencyDollarIcon,
  ShoppingCartIcon,
  UsersIcon 
} from '@heroicons/react/24/outline';

interface DashboardProps {
  stats: {
    totalSales: number;
    totalProducts: number;
    totalCustomers: number;
    totalRevenue: number;
  };
}

const StatCard = ({ 
  name, 
  stat, 
  icon: Icon, 
  change, 
  changeType 
}: {
  name: string;
  stat: string;
  icon: React.ElementType;
  change?: string;
  changeType?: 'increase' | 'decrease';
}) => (
  <div className="relative overflow-hidden rounded-lg bg-white px-4 pt-5 pb-12 shadow sm:px-6 sm:pt-6">
    <dt>
      <div className="absolute rounded-md bg-indigo-500 p-3">
        <Icon className="h-6 w-6 text-white" aria-hidden="true" />
      </div>
      <p className="ml-16 truncate text-sm font-medium text-gray-500">{name}</p>
    </dt>
    <dd className="ml-16 flex items-baseline pb-6 sm:pb-7">
      <p className="text-2xl font-semibold text-gray-900">{stat}</p>
      {change && (
        <p className={`ml-2 flex items-baseline text-sm font-semibold ${
          changeType === 'increase' ? 'text-green-600' : 'text-red-600'
        }`}>
          {change}
        </p>
      )}
    </dd>
  </div>
);

export default function Dashboard({ stats }: DashboardProps) {
  return (
    <AuthenticatedLayout title="Dashboard">
      <Head title="Dashboard" />

      <div className="space-y-8">
        {/* Stats */}
        <div>
          <h3 className="text-lg font-medium leading-6 text-gray-900 mb-6">
            Resumen General
          </h3>
          <dl className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <StatCard
              name="Ventas Totales"
              stat={stats.totalSales.toString()}
              icon={ShoppingCartIcon}
              change="+4.75%"
              changeType="increase"
            />
            <StatCard
              name="Productos"
              stat={stats.totalProducts.toString()}
              icon={ChartBarIcon}
              change="+2.02%"
              changeType="increase"
            />
            <StatCard
              name="Clientes"
              stat={stats.totalCustomers.toString()}
              icon={UsersIcon}
              change="+1.39%"
              changeType="increase"
            />
            <StatCard
              name="Ingresos"
              stat={`S/ ${stats.totalRevenue.toLocaleString('es-PE', { minimumFractionDigits: 2 })}`}
              icon={CurrencyDollarIcon}
              change="+3.14%"
              changeType="increase"
            />
          </dl>
        </div>

        {/* Quick Actions */}
        <div className="rounded-lg bg-white shadow">
          <div className="px-4 py-5 sm:p-6">
            <h3 className="text-lg font-medium leading-6 text-gray-900 mb-4">
              Acciones Rápidas
            </h3>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <button className="relative rounded-lg border border-gray-300 bg-white px-6 py-4 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow">
                <span className="flex items-center space-x-3">
                  <ChartBarIcon className="h-6 w-6 text-indigo-600" />
                  <span className="text-sm font-medium text-gray-900">Nueva Venta</span>
                </span>
              </button>
              
              <button className="relative rounded-lg border border-gray-300 bg-white px-6 py-4 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow">
                <span className="flex items-center space-x-3">
                  <CurrencyDollarIcon className="h-6 w-6 text-indigo-600" />
                  <span className="text-sm font-medium text-gray-900">Agregar Producto</span>
                </span>
              </button>
              
              <button className="relative rounded-lg border border-gray-300 bg-white px-6 py-4 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow">
                <span className="flex items-center space-x-3">
                  <UsersIcon className="h-6 w-6 text-indigo-600" />
                  <span className="text-sm font-medium text-gray-900">Nuevo Cliente</span>
                </span>
              </button>
              
              <button className="relative rounded-lg border border-gray-300 bg-white px-6 py-4 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow">
                <span className="flex items-center space-x-3">
                  <ChartBarIcon className="h-6 w-6 text-indigo-600" />
                  <span className="text-sm font-medium text-gray-900">Ver Reportes</span>
                </span>
              </button>
            </div>
          </div>
        </div>

        {/* Recent Activity */}
        <div className="rounded-lg bg-white shadow">
          <div className="px-4 py-5 sm:p-6">
            <h3 className="text-lg font-medium leading-6 text-gray-900 mb-4">
              Actividad Reciente
            </h3>
            <div className="flow-root">
              <ul className="-my-5 divide-y divide-gray-200">
                <li className="py-4">
                  <div className="flex items-center space-x-4">
                    <div className="flex-shrink-0">
                      <div className="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center">
                        <ShoppingCartIcon className="h-4 w-4 text-white" />
                      </div>
                    </div>
                    <div className="flex-1 min-w-0">
                      <p className="text-sm font-medium text-gray-900 truncate">
                        Nueva venta registrada
                      </p>
                      <p className="text-sm text-gray-500 truncate">
                        S/ 125.50 - Cliente: María García
                      </p>
                    </div>
                    <div className="flex-shrink-0 text-sm text-gray-500">
                      Hace 2 min
                    </div>
                  </div>
                </li>
                
                <li className="py-4">
                  <div className="flex items-center space-x-4">
                    <div className="flex-shrink-0">
                      <div className="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                        <ChartBarIcon className="h-4 w-4 text-white" />
                      </div>
                    </div>
                    <div className="flex-1 min-w-0">
                      <p className="text-sm font-medium text-gray-900 truncate">
                        Producto agregado
                      </p>
                      <p className="text-sm text-gray-500 truncate">
                        Producto: Pan Integral - Stock: 50
                      </p>
                    </div>
                    <div className="flex-shrink-0 text-sm text-gray-500">
                      Hace 15 min
                    </div>
                  </div>
                </li>
                
                <li className="py-4">
                  <div className="flex items-center space-x-4">
                    <div className="flex-shrink-0">
                      <div className="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center">
                        <UsersIcon className="h-4 w-4 text-white" />
                      </div>
                    </div>
                    <div className="flex-1 min-w-0">
                      <p className="text-sm font-medium text-gray-900 truncate">
                        Cliente registrado
                      </p>
                      <p className="text-sm text-gray-500 truncate">
                        Juan Pérez - juan@email.com
                      </p>
                    </div>
                    <div className="flex-shrink-0 text-sm text-gray-500">
                      Hace 1 hora
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
