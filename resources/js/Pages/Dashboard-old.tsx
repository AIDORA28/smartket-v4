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

export default function Dashboard({ auth, stats, recentSales, lowStock, topProducts, salesTrend, cajaStatus, empresa, sucursal, features }: DashboardProps) {
  const currentTime = new Date().toLocaleString('es-ES', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });

  // Calcular crecimiento simulado basado en datos de tendencia
  const ventasAyer = salesTrend?.[salesTrend.length - 2]?.sales || 0;
  const ventasHoyTrend = salesTrend?.[salesTrend.length - 1]?.sales || stats.ventasHoy;
  const crecimientoHoy = ventasAyer > 0 ? ((ventasHoyTrend - ventasAyer) / ventasAyer) * 100 : 0;

  return (
    <AuthenticatedLayout>
      <Head title="Dashboard - SmartKet" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
          
          {/* Header mejorado con información contextual */}
          <div className="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-6 text-white shadow-xl">
            <div className="flex items-center justify-between">
              <div>
                <h1 className="text-3xl font-bold mb-2">
                  ¡Hola, {auth.user.name.split(' ')[0]}! 👋
                </h1>
                <p className="text-indigo-100 text-lg mb-1">
                  Todo marcha bien en {empresa.nombre}
                </p>
                <p className="text-indigo-200 text-sm">
                  {currentTime} • {sucursal.nombre}
                </p>
              </div>
              
              {/* Estado de la caja */}
              <div className="text-right">
                {cajaStatus.activa ? (
                  <div className="bg-white/20 backdrop-blur-sm rounded-xl p-4">
                    <div className="flex items-center space-x-2 mb-2">
                      <div className="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                      <span className="font-semibold">Caja Activa</span>
                    </div>
                    <p className="text-sm text-indigo-200">{cajaStatus.caja_nombre}</p>
                    <p className="text-xs text-indigo-300">
                      Efectivo estimado: S/ {cajaStatus.total_estimado?.toFixed(2) || '0.00'}
                    </p>
                  </div>
                ) : (
                  <div className="bg-orange-500/20 backdrop-blur-sm rounded-xl p-4">
                    <div className="flex items-center space-x-2 mb-2">
                      <div className="w-3 h-3 bg-orange-400 rounded-full"></div>
                      <span className="font-semibold">Caja Cerrada</span>
                    </div>
                    <p className="text-xs text-orange-200">{cajaStatus.mensaje}</p>
                  </div>
                )}
              </div>
            </div>
          </div>

          {/* KPIs principales con diseño mejorado */}
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
              className="bg-gradient-to-br from-emerald-50 to-green-100 border-emerald-200 hover:shadow-xl transition-all duration-300"
            />
            
            <StatsCard
              title="📦 Productos"
              value={stats.productosStock}
              subtitle="En inventario"
              icon="products"
              color="blue"
              className="bg-gradient-to-br from-blue-50 to-indigo-100 border-blue-200 hover:shadow-xl transition-all duration-300"
            />
            
            <StatsCard
              title="👥 Clientes"
              value={stats.clientesActivos}
              subtitle="Activos este mes"
              icon="users"
              color="purple"
              className="bg-gradient-to-br from-purple-50 to-violet-100 border-purple-200 hover:shadow-xl transition-all duration-300"
            />
            
            <StatsCard
              title="📊 Mes Actual"
              value={`S/ ${stats.facturacionMensual.toLocaleString('es-PE', { minimumFractionDigits: 2 })}`}
              subtitle="Facturación"
              icon="chart"
              color="orange"
              className="bg-gradient-to-br from-amber-50 to-orange-100 border-orange-200 hover:shadow-xl transition-all duration-300"
            />
          </div>

          {/* Acciones rápidas mejoradas */}
          <Card className="border-0 shadow-xl bg-gradient-to-r from-slate-50 to-gray-50">
            <CardHeader className="bg-gradient-to-r from-gray-100 to-slate-100 border-b border-gray-200">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <div className="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <span className="text-white text-2xl">⚡</span>
                  </div>
                  <div>
                    <h3 className="text-xl font-bold text-gray-900">Acciones Rápidas</h3>
                    <p className="text-sm text-gray-600">Funciones principales de tu negocio</p>
                  </div>
                </div>
              </div>
            </CardHeader>
            <CardBody className="p-6">
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {features.pos && (
                  <Link href="/pos">
                    <Button variant="primary" size="lg" className="w-full justify-center h-24 bg-gradient-to-br from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-lg font-semibold shadow-xl transform hover:scale-105 transition-all duration-300">
                      <div className="text-center">
                        <div className="text-3xl mb-2">🛒</div>
                        <div>POS</div>
                        <div className="text-xs opacity-80">Punto de Venta</div>
                      </div>
                    </Button>
                  </Link>
                )}
                
                <Link href="/products">
                  <Button variant="secondary" size="lg" className="w-full justify-center h-24 bg-gradient-to-br from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-lg font-semibold shadow-xl transform hover:scale-105 transition-all duration-300">
                    <div className="text-center">
                      <div className="text-3xl mb-2">📦</div>
                      <div>Productos</div>
                      <div className="text-xs opacity-80">Inventario</div>
                    </div>
                  </Button>
                </Link>
                
                <Link href="/clients">
                  <Button variant="secondary" size="lg" className="w-full justify-center h-24 bg-gradient-to-br from-purple-500 to-violet-600 hover:from-purple-600 hover:to-violet-700 text-white text-lg font-semibold shadow-xl transform hover:scale-105 transition-all duration-300">
                    <div className="text-center">
                      <div className="text-3xl mb-2">👥</div>
                      <div>Clientes</div>
                      <div className="text-xs opacity-80">CRM</div>
                    </div>
                  </Button>
                </Link>
                
                {features.reportes && (
                  <Link href="/reports">
                    <Button variant="secondary" size="lg" className="w-full justify-center h-24 bg-gradient-to-br from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white text-lg font-semibold shadow-xl transform hover:scale-105 transition-all duration-300">
                      <div className="text-center">
                        <div className="text-3xl mb-2">📊</div>
                        <div>Reportes</div>
                        <div className="text-xs opacity-80">Analytics</div>
                      </div>
                    </Button>
                  </Link>
                )}
              </div>
            </CardBody>
          </Card>

          {/* Grid de información detallada */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {/* Ventas recientes con diseño mejorado */}
            <Card className="shadow-xl border-0">
              <CardHeader className="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100">
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-3">
                    <div className="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                      <span className="text-white text-lg">�</span>
                    </div>
                    <div>
                      <h3 className="text-lg font-bold text-gray-900">Ventas Recientes</h3>
                      <p className="text-sm text-gray-600">Últimas transacciones</p>
                    </div>
                  </div>
                  <Link href="/sales">
                    <Button variant="ghost" size="sm" className="text-green-600 hover:text-green-800 hover:bg-green-100 transition-colors">
                      Ver todas →
                    </Button>
                  </Link>
                </div>
              </CardHeader>
              <CardBody className="p-0">
                <div className="space-y-0">
                  {recentSales?.slice(0, 5).map((sale, index) => (
                    <div key={sale.id} className="flex items-center justify-between p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                      <div className="flex items-center space-x-3">
                        <div className="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                          {index + 1}
                        </div>
                        <div>
                          <p className="font-semibold text-gray-900">{sale.cliente}</p>
                          <p className="text-sm text-gray-600 flex items-center space-x-2">
                            <span>📦 {sale.productos} productos</span>
                            <span>•</span>
                            <span>🕒 {sale.fecha}</span>
                          </p>
                        </div>
                      </div>
                      <div className="text-right">
                        <p className="font-bold text-green-700 text-lg">
                          S/ {sale.total.toLocaleString('es-PE', { minimumFractionDigits: 2 })}
                        </p>
                      </div>
                    </div>
                  )) || (
                    <div className="text-center py-12">
                      <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span className="text-gray-400 text-2xl">💳</span>
                      </div>
                      <p className="text-gray-500 font-medium">No hay ventas recientes</p>
                    </div>
                  )}
                </div>
              </CardBody>
            </Card>

            {/* Productos con stock bajo */}
            <Card className="shadow-xl border-0">
              <CardHeader className="bg-gradient-to-r from-red-50 to-orange-50 border-b border-red-100">
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-3">
                    <div className="w-10 h-10 bg-gradient-to-br from-red-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                      <span className="text-white text-lg">⚠️</span>
                    </div>
                    <div>
                      <h3 className="text-lg font-bold text-gray-900">Stock Bajo</h3>
                      <p className="text-sm text-gray-600">Productos que necesitan reposición</p>
                    </div>
                  </div>
                  <Link href="/products">
                    <Button variant="ghost" size="sm" className="text-red-600 hover:text-red-800 hover:bg-red-100 transition-colors">
                      Ver inventario →
                    </Button>
                  </Link>
                </div>
              </CardHeader>
              <CardBody className="p-0">
                <div className="space-y-0">
                  {lowStock?.slice(0, 5).map((product) => (
                    <div key={product.id} className="flex items-center justify-between p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                      <div className="flex items-center space-x-3">
                        <div className="w-10 h-10 bg-gradient-to-br from-red-500 to-orange-500 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                          !
                        </div>
                        <div>
                          <p className="font-semibold text-gray-900">{product.nombre}</p>
                          <p className="text-sm text-gray-600">
                            Mínimo: {product.minimo} unidades
                          </p>
                        </div>
                      </div>
                      <div className="text-right">
                        <p className={`font-bold text-lg ${product.stock === 0 ? 'text-red-700' : 'text-orange-600'}`}>
                          {product.stock}
                        </p>
                        <p className={`text-xs font-medium ${product.stock === 0 ? 'text-red-500' : 'text-orange-500'}`}>
                          {product.stock === 0 ? 'Sin stock' : 'Stock bajo'}
                        </p>
                      </div>
                    </div>
                  )) || (
                    <div className="text-center py-12">
                      <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span className="text-green-600 text-2xl">✅</span>
                      </div>
                      <p className="text-green-700 font-semibold">¡Excelente!</p>
                      <p className="text-gray-600 text-sm mt-1">Stock suficiente en todos los productos</p>
                    </div>
                  )}
                </div>
              </CardBody>
            </Card>
          </div>

          {/* Productos más vendidos */}
          {topProducts && topProducts.length > 0 && (
            <Card className="shadow-xl border-0">
              <CardHeader className="bg-gradient-to-r from-purple-50 to-violet-50 border-b border-purple-100">
                <div className="flex items-center space-x-3">
                  <div className="w-10 h-10 bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg">
                    <span className="text-white text-lg">🏆</span>
                  </div>
                  <div>
                    <h3 className="text-lg font-bold text-gray-900">Productos Más Vendidos</h3>
                    <p className="text-sm text-gray-600">Los favoritos de tus clientes (últimos 30 días)</p>
                  </div>
                </div>
              </CardHeader>
              <CardBody>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  {topProducts.slice(0, 6).map((product, index) => (
                    <div key={product.id} className="bg-gradient-to-br from-gray-50 to-white p-4 rounded-xl border border-gray-100 hover:shadow-md transition-shadow">
                      <div className="flex items-center justify-between mb-2">
                        <span className={`inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white ${
                          index === 0 ? 'bg-yellow-500' : index === 1 ? 'bg-gray-400' : index === 2 ? 'bg-amber-600' : 'bg-blue-500'
                        }`}>
                          {index + 1}
                        </span>
                        <span className="text-sm font-bold text-purple-600">
                          {product.total_vendido} vendidos
                        </span>
                      </div>
                      <h4 className="font-semibold text-gray-900 mb-1">{product.nombre}</h4>
                      <p className="text-sm text-gray-600">
                        Ingresos: S/ {product.ingresos.toLocaleString('es-PE', { minimumFractionDigits: 2 })}
                      </p>
                    </div>
                  ))}
                </div>
              </CardBody>
            </Card>
          )}

          {/* Footer mejorado */}
          <div className="text-center py-8">
            <div className="inline-flex items-center space-x-6 bg-white rounded-2xl px-8 py-4 shadow-xl border border-gray-100">
              <div className="flex items-center space-x-2">
                <div className="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <span className="text-sm font-semibold text-gray-700">SmartKet ERP v4.0</span>
              </div>
              <div className="w-px h-6 bg-gray-300"></div>
              <span className="text-xs text-gray-500">💪 Sistema optimizado</span>
              <div className="w-px h-6 bg-gray-300"></div>
              <span className="text-xs text-gray-500">�️ 100% seguro</span>
            </div>
          </div>

        </div>
      </div>
    </AuthenticatedLayout>
  );
}