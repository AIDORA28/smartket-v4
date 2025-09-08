import React from 'react';
import { StatsCard } from '../ui/StatsCard';

interface DashboardKPIsProps {
  stats: {
    ventasHoy: number;
    productosStock: number;
    clientesActivos: number;
    facturacionMensual: number;
  };
  salesTrend?: Array<{
    date: string;
    day: string;
    sales: number;
  }>;
}

export default function DashboardKPIs({ stats, salesTrend }: DashboardKPIsProps) {
  // Calcular crecimiento simulado basado en datos de tendencia
  const ventasAyer = salesTrend?.[salesTrend.length - 2]?.sales || 0;
  const ventasHoyTrend = salesTrend?.[salesTrend.length - 1]?.sales || stats.ventasHoy;
  const crecimientoHoy = ventasAyer > 0 ? ((ventasHoyTrend - ventasAyer) / ventasAyer) * 100 : 0;

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <StatsCard
        title="💰 Ventas Hoy"
        value={`S/ ${stats.ventasHoy.toLocaleString('es-PE', { minimumFractionDigits: 2 })}`}
        icon="money"
        trend={{
          value: Math.abs(crecimientoHoy),
          label: `${crecimientoHoy >= 0 ? '+' : '-'}${Math.abs(crecimientoHoy).toFixed(1)}% vs ayer`,
          direction: crecimientoHoy >= 0 ? "up" : "down"
        }}
        color="green"
        className="bg-gradient-to-br from-red-50 to-red-100 border-red-200 hover:shadow-xl transition-all duration-300"
      />
      
      <StatsCard
        title="📦 Productos"
        value={stats.productosStock}
        subtitle="En inventario"
        icon="products"
        color="blue"
        className="bg-gradient-to-br from-amber-50 to-amber-100 border-amber-200 hover:shadow-xl transition-all duration-300"
      />
      
      <StatsCard
        title="👥 Clientes"
        value={stats.clientesActivos}
        subtitle="Activos este mes"
        icon="users"
        color="purple"
        className="bg-gradient-to-br from-red-50 to-red-100 border-red-200 hover:shadow-xl transition-all duration-300"
      />
      
      <StatsCard
        title="📊 Mes Actual"
        value={`S/ ${stats.facturacionMensual.toLocaleString('es-PE', { minimumFractionDigits: 2 })}`}
        subtitle="Facturación"
        icon="chart"
        color="orange"
        className="bg-gradient-to-br from-amber-50 to-amber-100 border-amber-200 hover:shadow-xl transition-all duration-300"
      />
    </div>
  );
}
