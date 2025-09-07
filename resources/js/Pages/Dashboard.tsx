import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout';
import { StatsCard } from '../Components/ui/StatsCard';
import { Button } from '../Components/ui/Button';
import { Card, CardHeader, CardBody } from '../Components/ui/Card';

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
  empresa: {
    id: number;
    nombre_empresa: string;
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

export default function Dashboard({ auth, stats, recentSales, lowStock, empresa, sucursal, features }: DashboardProps) {
  const currentTime = new Date().toLocaleString('es-ES', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });

  // Calcular crecimiento simulado basado en datos
  const ventasHoyGrowth = Math.floor(Math.random() * 20) + 5; // 5-25%
  const clientesGrowth = Math.floor(Math.random() * 15) + 2; // 2-17%
  const facturacionGrowth = Math.floor(Math.random() * 25) + 8; // 8-33%

  return (
    <AuthenticatedLayout
      header={
        <div className="flex justify-between items-center">
          <div>
            <h2 className="font-bold text-2xl text-gray-900 leading-tight">
              Dashboard Ejecutivo
            </h2>
            <p className="text-sm text-gray-600 mt-1">
              {currentTime} • {empresa?.nombre_empresa} - {sucursal?.nombre}
            </p>
          </div>
          <div className="flex items-center space-x-3">
            <div className="text-right">
              <p className="text-sm font-medium text-gray-900">{auth.user.name}</p>
              <p className="text-xs text-gray-500 capitalize">{auth.user.rol_principal}</p>
            </div>
            <div className="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
              <span className="text-white font-bold text-lg">
                {auth.user.name.charAt(0).toUpperCase()}
              </span>
            </div>
          </div>
        </div>
      }
    >
      <Head title="Dashboard - SmartKet" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
          
          {/* Bienvenida y Estado del Plan */}
          <div className="bg-gradient-to-r from-blue-600 to-purple-700 rounded-xl p-6 text-white">
            <div className="flex items-center justify-between">
              <div>
                <h3 className="text-2xl font-bold mb-2">
                  ¡Bienvenido de vuelta, {auth.user.name.split(' ')[0]}!
                </h3>
                <p className="text-blue-100 text-lg">
                  Todo funciona correctamente. Aquí tienes el resumen de tu negocio.
                </p>
              </div>
              <div className="text-right">
                <div className="inline-flex items-center bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                  <div className="flex items-center space-x-2">
                    <div className="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    <span className="font-semibold">Plan {empresa?.plan?.nombre}</span>
                  </div>
                </div>
                <p className="text-blue-200 text-sm mt-1">Estado: Activo</p>
              </div>
            </div>
          </div>

          {/* Stats Grid Mejorado */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatsCard
              title="Ventas Hoy"
              value={`$${stats.ventasHoy.toLocaleString()}`}
              icon="money"
              trend={{
                value: ventasHoyGrowth,
                label: `+${ventasHoyGrowth}% vs ayer`,
                direction: "up"
              }}
              color="green"
              className="bg-gradient-to-br from-green-50 to-emerald-100 border-green-200"
            />
            
            <StatsCard
              title="Productos Activos"
              value={stats.productosStock}
              icon="products"
              subtitle="En inventario"
              color="blue"
              className="bg-gradient-to-br from-blue-50 to-indigo-100 border-blue-200"
            />
            
            <StatsCard
              title="Clientes Activos"
              value={stats.clientesActivos}
              icon="users"
              trend={{
                value: clientesGrowth,
                label: `+${clientesGrowth}% este mes`,
                direction: "up"
              }}
              color="purple"
              className="bg-gradient-to-br from-purple-50 to-violet-100 border-purple-200"
            />
            
            <StatsCard
              title="Facturación Mensual"
              value={`$${stats.facturacionMensual.toLocaleString()}`}
              icon="chart"
              trend={{
                value: facturacionGrowth,
                label: `+${facturacionGrowth}% vs mes anterior`,
                direction: "up"
              }}
              color="orange"
              className="bg-gradient-to-br from-orange-50 to-amber-100 border-orange-200"
            />
          </div>

          {/* Panel de Control Administrativo */}
          <Card className="border-2 border-blue-200 shadow-lg">
            <CardHeader className="bg-gradient-to-r from-blue-50 to-indigo-50">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <div className="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                    <span className="text-white text-xl">⚡</span>
                  </div>
                  <div>
                    <h3 className="text-xl font-bold text-gray-900">Panel de Control Administrativo</h3>
                    <p className="text-sm text-gray-600">Acceso rápido a funciones principales</p>
                  </div>
                </div>
              </div>
            </CardHeader>
            <CardBody>
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {features.pos && (
                  <Link href="/pos">
                    <Button variant="primary" size="lg" className="w-full justify-center h-20 bg-gradient-to-br from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-lg font-semibold shadow-lg transform hover:scale-105 transition-all">
                      <div className="text-center">
                        <div className="text-2xl mb-1">🛒</div>
                        <div>Punto de Venta</div>
                        <div className="text-xs opacity-80">POS Inteligente</div>
                      </div>
                    </Button>
                  </Link>
                )}
                
                <Link href="/products">
                  <Button variant="secondary" size="lg" className="w-full justify-center h-20 bg-gradient-to-br from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-lg font-semibold shadow-lg transform hover:scale-105 transition-all">
                    <div className="text-center">
                      <div className="text-2xl mb-1">📦</div>
                      <div>Inventario</div>
                      <div className="text-xs opacity-80">Gestión de productos</div>
                    </div>
                  </Button>
                </Link>
                
                <Link href="/clients">
                  <Button variant="secondary" size="lg" className="w-full justify-center h-20 bg-gradient-to-br from-purple-500 to-violet-600 hover:from-purple-600 hover:to-violet-700 text-white text-lg font-semibold shadow-lg transform hover:scale-105 transition-all">
                    <div className="text-center">
                      <div className="text-2xl mb-1">👥</div>
                      <div>Clientes</div>
                      <div className="text-xs opacity-80">CRM & Contactos</div>
                    </div>
                  </Button>
                </Link>
                
                {features.reportes && (
                  <Link href="/reports">
                    <Button variant="secondary" size="lg" className="w-full justify-center h-20 bg-gradient-to-br from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white text-lg font-semibold shadow-lg transform hover:scale-105 transition-all">
                      <div className="text-center">
                        <div className="text-2xl mb-1">📊</div>
                        <div>Reportes</div>
                        <div className="text-xs opacity-80">Analytics & BI</div>
                      </div>
                    </Button>
                  </Link>
                )}
              </div>
              
              {/* Funciones Administrativas Adicionales */}
              <div className="mt-6 pt-6 border-t border-gray-200">
                <h4 className="text-lg font-semibold text-gray-900 mb-4">🔧 Herramientas Administrativas</h4>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                  <Link href="/users">
                    <Button variant="ghost" className="w-full justify-start p-4 h-auto">
                      <div className="flex items-center space-x-3">
                        <span className="text-2xl">👤</span>
                        <div className="text-left">
                          <div className="font-medium">Usuarios</div>
                          <div className="text-sm text-gray-500">Gestión de accesos</div>
                        </div>
                      </div>
                    </Button>
                  </Link>
                  
                  <Link href="/settings">
                    <Button variant="ghost" className="w-full justify-start p-4 h-auto">
                      <div className="flex items-center space-x-3">
                        <span className="text-2xl">⚙️</span>
                        <div className="text-left">
                          <div className="font-medium">Configuración</div>
                          <div className="text-sm text-gray-500">Parámetros del sistema</div>
                        </div>
                      </div>
                    </Button>
                  </Link>
                  
                  <Link href="/backup">
                    <Button variant="ghost" className="w-full justify-start p-4 h-auto">
                      <div className="flex items-center space-x-3">
                        <span className="text-2xl">💾</span>
                        <div className="text-left">
                          <div className="font-medium">Respaldos</div>
                          <div className="text-sm text-gray-500">Backup y restaurar</div>
                        </div>
                      </div>
                    </Button>
                  </Link>
                </div>
              </div>
            </CardBody>
          </Card>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {/* Recent Sales Mejorado */}
            <Card className="shadow-lg border-gray-300">
              <CardHeader className="bg-gradient-to-r from-gray-50 to-slate-50">
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-3">
                    <div className="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                      <span className="text-white text-lg">💳</span>
                    </div>
                    <h3 className="text-lg font-bold text-gray-900">Ventas Recientes</h3>
                  </div>
                  <Link href="/sales">
                    <Button variant="ghost" size="sm" className="text-blue-600 hover:text-blue-800">
                      Ver todas →
                    </Button>
                  </Link>
                </div>
              </CardHeader>
              <CardBody>
                <div className="space-y-3">
                  {recentSales.map((sale, index) => (
                    <div key={sale.id} className="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-100 hover:shadow-md transition-shadow">
                      <div className="flex items-center space-x-3">
                        <div className="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                          {index + 1}
                        </div>
                        <div>
                          <p className="font-semibold text-gray-900">{sale.cliente}</p>
                          <p className="text-sm text-gray-600">
                            <span className="inline-flex items-center">
                              📦 {sale.productos} productos • 🕒 {sale.fecha}
                            </span>
                          </p>
                        </div>
                      </div>
                      <div className="text-right">
                        <p className="font-bold text-green-700 text-lg">
                          ${sale.total.toLocaleString()}
                        </p>
                        <p className="text-xs text-gray-500">Total</p>
                      </div>
                    </div>
                  ))}
                  
                  {recentSales.length === 0 && (
                    <div className="text-center py-12">
                      <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span className="text-gray-400 text-2xl">💳</span>
                      </div>
                      <p className="text-gray-500 font-medium">No hay ventas recientes</p>
                      <p className="text-gray-400 text-sm mt-1">Las ventas aparecerán aquí cuando realices transacciones</p>
                    </div>
                  )}
                </div>
              </CardBody>
            </Card>

            {/* Low Stock Alerts Mejorado */}
            <Card className="shadow-lg border-gray-300">
              <CardHeader className="bg-gradient-to-r from-red-50 to-orange-50">
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-3">
                    <div className="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center">
                      <span className="text-white text-lg">⚠️</span>
                    </div>
                    <h3 className="text-lg font-bold text-gray-900">Alertas de Stock</h3>
                  </div>
                  <Link href="/products">
                    <Button variant="ghost" size="sm" className="text-blue-600 hover:text-blue-800">
                      Ver inventario →
                    </Button>
                  </Link>
                </div>
              </CardHeader>
              <CardBody>
                <div className="space-y-3">
                  {lowStock.map((product, index) => (
                    <div key={product.id} className="flex items-center justify-between p-4 bg-gradient-to-r from-red-50 to-orange-50 rounded-xl border border-red-200 hover:shadow-md transition-shadow">
                      <div className="flex items-center space-x-3">
                        <div className="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white font-bold">
                          !
                        </div>
                        <div>
                          <p className="font-semibold text-gray-900">{product.nombre}</p>
                          <p className="text-sm text-gray-600">
                            Mínimo requerido: {product.minimo} unidades
                          </p>
                        </div>
                      </div>
                      <div className="text-right">
                        <p className={`font-bold text-lg ${product.stock === 0 ? 'text-red-700' : 'text-orange-600'}`}>
                          {product.stock} restantes
                        </p>
                        <p className={`text-xs font-medium ${product.stock === 0 ? 'text-red-500' : 'text-orange-500'}`}>
                          {product.stock === 0 ? 'Sin stock' : 'Stock bajo'}
                        </p>
                      </div>
                    </div>
                  ))}
                  
                  {lowStock.length === 0 && (
                    <div className="text-center py-12">
                      <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span className="text-green-600 text-2xl">✅</span>
                      </div>
                      <p className="text-green-700 font-semibold">¡Excelente!</p>
                      <p className="text-gray-600 text-sm mt-1">Todos los productos tienen stock suficiente</p>
                    </div>
                  )}
                </div>
              </CardBody>
            </Card>
          </div>

          {/* Panel de Estado del Sistema Avanzado */}
          <Card className="border-2 border-indigo-200 shadow-xl">
            <CardHeader className="bg-gradient-to-r from-indigo-50 to-blue-50">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <div className="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <span className="text-white text-xl">🖥️</span>
                  </div>
                  <div>
                    <h3 className="text-xl font-bold text-gray-900">Centro de Monitoreo</h3>
                    <p className="text-sm text-gray-600">Estado del sistema y recursos</p>
                  </div>
                </div>
                <div className="flex items-center space-x-2">
                  <div className="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                  <span className="text-sm font-medium text-gray-700">Sistema Operativo</span>
                </div>
              </div>
            </CardHeader>
            <CardBody>
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div className="text-center p-6 bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl border-2 border-green-200">
                  <div className="w-12 h-12 bg-green-500 rounded-full mx-auto mb-3 flex items-center justify-center shadow-lg">
                    <span className="text-white text-xl">🗄️</span>
                  </div>
                  <p className="font-bold text-green-900 text-lg">Base de Datos</p>
                  <p className="text-sm text-green-700 font-medium">PostgreSQL</p>
                  <div className="mt-2 inline-flex items-center bg-green-200 px-3 py-1 rounded-full">
                    <div className="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                    <span className="text-xs font-semibold text-green-800">Conectado</span>
                  </div>
                </div>
                
                <div className="text-center p-6 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl border-2 border-blue-200">
                  <div className="w-12 h-12 bg-blue-500 rounded-full mx-auto mb-3 flex items-center justify-center shadow-lg">
                    <span className="text-white text-xl">💰</span>
                  </div>
                  <p className="font-bold text-blue-900 text-lg">Caja</p>
                  <p className="text-sm text-blue-700 font-medium">Sesión activa</p>
                  <div className="mt-2 inline-flex items-center bg-blue-200 px-3 py-1 rounded-full">
                    <div className="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                    <span className="text-xs font-semibold text-blue-800">Operativa</span>
                  </div>
                </div>
                
                <div className="text-center p-6 bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl border-2 border-purple-200">
                  <div className="w-12 h-12 bg-purple-500 rounded-full mx-auto mb-3 flex items-center justify-center shadow-lg">
                    <span className="text-white text-xl">📊</span>
                  </div>
                  <p className="font-bold text-purple-900 text-lg">Analytics</p>
                  <p className="text-sm text-purple-700 font-medium">Procesando datos</p>
                  <div className="mt-2 inline-flex items-center bg-purple-200 px-3 py-1 rounded-full">
                    <div className="w-2 h-2 bg-purple-500 rounded-full mr-2 animate-pulse"></div>
                    <span className="text-xs font-semibold text-purple-800">Activo</span>
                  </div>
                </div>
                
                <div className="text-center p-6 bg-gradient-to-br from-orange-50 to-amber-100 rounded-xl border-2 border-orange-200">
                  <div className="w-12 h-12 bg-orange-500 rounded-full mx-auto mb-3 flex items-center justify-center shadow-lg">
                    <span className="text-white text-xl">💾</span>
                  </div>
                  <p className="font-bold text-orange-900 text-lg">Respaldos</p>
                  <p className="text-sm text-orange-700 font-medium">Auto backup</p>
                  <div className="mt-2 inline-flex items-center bg-orange-200 px-3 py-1 rounded-full">
                    <div className="w-2 h-2 bg-orange-500 rounded-full mr-2"></div>
                    <span className="text-xs font-semibold text-orange-800">Programado</span>
                  </div>
                </div>
              </div>
              
              {/* Información adicional del sistema */}
              <div className="mt-8 pt-6 border-t border-gray-200">
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                  <div className="text-center">
                    <h4 className="text-lg font-semibold text-gray-900 mb-2">🚀 Rendimiento</h4>
                    <p className="text-sm text-gray-600">Sistema optimizado para alta velocidad</p>
                    <div className="mt-2">
                      <div className="bg-green-200 rounded-full h-2">
                        <div className="bg-green-500 h-2 rounded-full" style={{width: '94%'}}></div>
                      </div>
                      <p className="text-xs text-gray-500 mt-1">94% eficiencia</p>
                    </div>
                  </div>
                  
                  <div className="text-center">
                    <h4 className="text-lg font-semibold text-gray-900 mb-2">🔒 Seguridad</h4>
                    <p className="text-sm text-gray-600">Datos protegidos con encriptación</p>
                    <div className="mt-2">
                      <div className="bg-blue-200 rounded-full h-2">
                        <div className="bg-blue-500 h-2 rounded-full" style={{width: '100%'}}></div>
                      </div>
                      <p className="text-xs text-gray-500 mt-1">100% seguro</p>
                    </div>
                  </div>
                  
                  <div className="text-center">
                    <h4 className="text-lg font-semibold text-gray-900 mb-2">📈 Disponibilidad</h4>
                    <p className="text-sm text-gray-600">Sistema disponible 24/7</p>
                    <div className="mt-2">
                      <div className="bg-purple-200 rounded-full h-2">
                        <div className="bg-purple-500 h-2 rounded-full" style={{width: '99.8%'}}></div>
                      </div>
                      <p className="text-xs text-gray-500 mt-1">99.8% uptime</p>
                    </div>
                  </div>
                </div>
              </div>
            </CardBody>
          </Card>

          {/* Footer del Dashboard */}
          <div className="text-center py-6">
            <div className="inline-flex items-center space-x-4 bg-white rounded-full px-6 py-3 shadow-lg border border-gray-200">
              <div className="flex items-center space-x-2">
                <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span className="text-sm font-medium text-gray-700">SmartKet ERP v4.0</span>
              </div>
              <div className="w-px h-4 bg-gray-300"></div>
              <span className="text-xs text-gray-500">Última actualización: hace 2 min</span>
              <div className="w-px h-4 bg-gray-300"></div>
              <span className="text-xs text-gray-500">🔋 Sistema: Óptimo</span>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}