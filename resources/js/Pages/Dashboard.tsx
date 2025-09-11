import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout';

// Dashboard Components
import OwnerDashboard from '@/Components/dashboard/OwnerDashboard';
import EmployeeDashboard from '@/Components/dashboard/EmployeeDashboard';

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
  
  // Determinar si es Owner o Empleado
  const isOwner = auth.user.rol_principal === 'owner';
  
  // 🐛 DEBUG: Verificar qué rol se está detectando
  console.log('🔍 DEBUG Dashboard:', {
    user: auth.user.name,
    rol: auth.user.rol_principal,
    isOwner: isOwner,
    willShow: isOwner ? 'OwnerDashboard' : 'EmployeeDashboard'
  });
  
  // Props comunes para ambos dashboards
  const commonProps = {
    auth,
    empresa,
    sucursal,
    stats
  };

  // Props específicos para Owner
  const ownerProps = {
    ...commonProps,
    sucursales: [
      {
        id: 1,
        nombre: sucursal.nombre,
        ventas_hoy: stats.ventasHoy,
        status: 'active' as const
      }
    ],
    alertas: {
      stock_bajo: lowStock?.length || 0,
      ventas_objetivo: stats.ventasHoy > 500,
      plan_limite: false
    }
  };

  // Props específicos para Empleados
  const employeeProps = {
    ...commonProps,
    miStats: {
      ventas_hoy: stats.ventasHoy,
      clientes_atendidos: Math.floor(stats.clientesActivos / 4), // Simulado
      productos_vendidos: topProducts?.length || 0,
      turno_inicio: '08:00'
    },
    tareasPendientes: lowStock?.slice(0, 3).map((item, index) => ({
      id: index + 1,
      titulo: `Revisar stock de ${item.nombre}`,
      tipo: 'inventario' as const,
      urgencia: item.stock < item.minimo / 2 ? 'alta' as const : 'media' as const,
      tiempo_estimado: '5 min'
    })) || []
  };

  return (
    <AuthenticatedLayout>
      <Head title="Dashboard - SmartKet" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          {isOwner ? (
            <OwnerDashboard {...ownerProps} />
          ) : (
            <EmployeeDashboard {...employeeProps} />
          )}
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
