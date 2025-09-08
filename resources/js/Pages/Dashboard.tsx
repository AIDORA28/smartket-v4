import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout';

// Dashboard Components
import DashboardHeader from '../Components/dashboard/DashboardHeader';
import DashboardKPIs from '../Components/dashboard/DashboardKPIs';
import QuickActions from '../Components/dashboard/QuickActions';
import RecentSales from '../Components/dashboard/RecentSales';
import LowStockAlerts from '../Components/dashboard/LowStockAlerts';
import TopProducts from '../Components/dashboard/TopProducts';
import InventoryOverview from '../Components/dashboard/InventoryOverview';
import RecentActivity from '../Components/dashboard/RecentActivity';
import DashboardFooter from '../Components/dashboard/DashboardFooter';

interface DashboardProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
      rol_principal: string;
    };
  };
  stats: {
    ventasHoy: number;
    productosStock: number;
    clientesActivos: number;
    facturacionMensual: number;
  };
  recentSales: Array<{
    id: number;
    cliente: string;
    total: number;
    fecha: string;
    productos: number;
  }>;
  lowStock: Array<{
    id: number;
    nombre: string;
    stock: number;
    minimo: number;
  }>;
  topProducts: Array<{
    id: number;
    nombre: string;
    total_vendido: number;
    ingresos: number;
  }>;
  salesTrend: Array<{
    date: string;
    day: string;
    sales: number;
  }>;
  cajaStatus: {
    activa: boolean;
    caja_nombre?: string;
    codigo?: string;
    monto_inicial?: number;
    ventas_efectivo_hoy?: number;
    total_estimado?: number;
    mensaje?: string;
  };
  // ✅ NUEVOS PROPS CON DATOS REALES
  inventoryOverview: {
    totalProductos: number;
    totalCategorias: number;
    totalMarcas: number;
    totalUnidades: number;
    marcasPopulares?: string[];
    categoriasPopulares?: string[];
  };
  recentActivity: Array<{
    type: string;
    message: string;
    time: string;
    icon: string;
    color: string;
  }>;
  empresa: {
    id: number;
    nombre_empresa: string;
    nombre: string;
    plan: {
      nombre: string;
    };
  };
  sucursal: {
    id: number;
    nombre: string;
  };
  features: {
    pos: boolean;
    inventario_avanzado: boolean;
    reportes: boolean;
    facturacion_electronica: boolean;
  };
}

export default function Dashboard({ 
  auth, 
  stats, 
  recentSales, 
  lowStock, 
  topProducts, 
  salesTrend, 
  cajaStatus, 
  inventoryOverview,
  recentActivity,
  empresa, 
  sucursal, 
  features 
}: DashboardProps) {
  return (
    <AuthenticatedLayout>
      <Head title="Dashboard - SmartKet" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
          
          {/* Header con información contextual */}
          <DashboardHeader
            userName={auth.user.name}
            empresaNombre={empresa.nombre}
            sucursalNombre={sucursal.nombre}
            cajaStatus={cajaStatus}
          />

          {/* KPIs principales */}
          <DashboardKPIs 
            stats={stats}
            salesTrend={salesTrend}
          />

          {/* ✅ NUEVO: Resumen de inventario con datos reales */}
          <InventoryOverview inventoryOverview={inventoryOverview} />

          {/* Acciones rápidas */}
          <QuickActions features={features} />

          {/* Grid de información detallada */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <RecentSales recentSales={recentSales} />
            <LowStockAlerts lowStock={lowStock} />
          </div>

          {/* Grid adicional con más información */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <TopProducts topProducts={topProducts} />
            {/* ✅ NUEVO: Actividad reciente */}
            <RecentActivity recentActivity={recentActivity} />
          </div>

          {/* Footer */}
          <DashboardFooter />

        </div>
      </div>
    </AuthenticatedLayout>
  );
}
