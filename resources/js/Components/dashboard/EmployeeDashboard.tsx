import React from 'react';
import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import MetricCard from '../core/shared/MetricCard';
import ActionCard from '../core/shared/ActionCard';
import {
  CurrencyDollarIcon,
  UsersIcon,
  ShoppingCartIcon,
  ClockIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  CubeIcon,
  BanknotesIcon,
  TagIcon,
  UserIcon
} from '@heroicons/react/24/outline';

interface EmployeeDashboardProps {
  auth: {
    user: {
      name: string;
      rol_principal: string;
    };
  };
  empresa: {
    nombre: string;
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
  miStats?: {
    ventas_hoy?: number;
    clientes_atendidos?: number;
    productos_vendidos?: number;
    turno_inicio?: string;
  };
  tareasPendientes?: Array<{
    id: number;
    titulo: string;
    tipo: 'venta' | 'inventario' | 'caja' | 'cliente';
    urgencia: 'alta' | 'media' | 'baja';
    tiempo_estimado?: string;
  }>;
  accesosRapidos?: Array<{
    titulo: string;
    url: string;
    emoji: string;
    descripcion: string;
    disponible: boolean;
  }>;
}

const EmployeeDashboard: React.FC<EmployeeDashboardProps> = ({
  auth,
  empresa,
  sucursal,
  stats,
  miStats = {},
  tareasPendientes = [],
  accesosRapidos = []
}) => {

  const formatCurrency = (amount: number = 0) => {
    return new Intl.NumberFormat('es-PE', {
      style: 'currency',
      currency: 'PEN',
      minimumFractionDigits: 0
    }).format(amount);
  };

  const getRoleEmoji = (role: string) => {
    const emojis = {
      'cajero': '💳',
      'vendedor': '🛒',
      'almacenero': '📦',
      'admin': '👔',
      'default': '👤'
    };
    return emojis[role as keyof typeof emojis] || emojis.default;
  };

  const getRoleTitle = (role: string) => {
    const titles = {
      'cajero': 'Cajero/a',
      'vendedor': 'Vendedor/a',
      'almacenero': 'Almacenero/a',
      'admin': 'Administrador/a',
      'default': 'Empleado/a'
    };
    return titles[role as keyof typeof titles] || titles.default;
  };

  // Accesos rápidos por defecto según el rol - PREPARADOS PARA BACKEND
  const getDefaultAccesos = (role: string) => {
    const accesos = {
      'cajero': [
        { titulo: 'Abrir Caja', url: '/cajas', emoji: '💳', descripcion: 'Iniciar turno de caja', disponible: false },
        { titulo: 'Nueva Venta', url: '/pos', emoji: '🛒', descripcion: 'Punto de venta', disponible: false },
        { titulo: 'Consultar Precios', url: '/productos', emoji: '🏷️', descripcion: 'Ver precios de productos', disponible: false },
        { titulo: 'Clientes', url: '/clientes', emoji: '👥', descripcion: 'Gestionar clientes', disponible: false }
      ],
      'vendedor': [
        { titulo: 'Nueva Venta', url: '/pos', emoji: '🛒', descripcion: 'Punto de venta', disponible: false },
        { titulo: 'Clientes', url: '/clientes', emoji: '👥', descripcion: 'Gestionar clientes', disponible: false },
        { titulo: 'Productos', url: '/productos', emoji: '📦', descripcion: 'Catálogo de productos', disponible: false },
        { titulo: 'Mis Ventas', url: '/ventas', emoji: '📊', descripcion: 'Ver mis ventas', disponible: false }
      ],
      'almacenero': [
        { titulo: 'Inventario', url: '/inventario', emoji: '📦', descripcion: 'Gestionar stock', disponible: false },
        { titulo: 'Productos', url: '/productos', emoji: '🏷️', descripcion: 'Administrar productos', disponible: false },
        { titulo: 'Compras', url: '/compras', emoji: '🛍️', descripcion: 'Gestionar compras', disponible: false },
        { titulo: 'Proveedores', url: '/proveedores', emoji: '🚚', descripcion: 'Gestionar proveedores', disponible: false }
      ],
      'admin': [
        { titulo: 'Panel General', url: '/dashboard', emoji: '📊', descripcion: 'Vista completa', disponible: true },
        { titulo: 'Módulo Core', url: '/core/company/settings', emoji: '⚙️', descripcion: 'Gestión empresarial', disponible: true },
        { titulo: 'Analytics', url: '/core/analytics', emoji: '📈', descripcion: 'Análisis y métricas', disponible: true },
        { titulo: 'Usuarios', url: '/core/users', emoji: '👥', descripcion: 'Gestionar usuarios', disponible: true }
      ]
    };
    
    return accesos[role as keyof typeof accesos] || accesos['vendedor'];
  };

  const accesos = accesosRapidos.length > 0 ? accesosRapidos : getDefaultAccesos(auth?.user?.rol_principal || 'vendedor');

  const getUrgenciaColor = (urgencia: string) => {
    const colors = {
      'alta': 'bg-red-100 text-red-800 border-red-200',
      'media': 'bg-yellow-100 text-yellow-800 border-yellow-200',
      'baja': 'bg-green-100 text-green-800 border-green-200'
    };
    return colors[urgencia as keyof typeof colors] || colors.baja;
  };

  return (
    <div className="space-y-8">
      {/* Header Personal */}
      <div className="bg-gradient-to-r from-green-600 to-blue-600 rounded-2xl p-6 text-white">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold">
              {getRoleEmoji(auth?.user?.rol_principal || 'default')} ¡Hola, {auth?.user?.name || 'Usuario'}!
            </h1>
            <p className="text-green-100 text-lg">
              {getRoleTitle(auth?.user?.rol_principal || 'default')} en {empresa?.nombre || 'tu empresa'}
            </p>
          </div>
          <div className="text-right">
            <p className="text-green-100 text-sm">
              📍 {sucursal?.nombre || 'Sucursal Principal'}
            </p>
            {miStats?.turno_inicio && (
              <p className="text-green-200 text-sm">
                🕐 Desde {miStats.turno_inicio}
              </p>
            )}
          </div>
        </div>
      </div>

      {/* Mis Estadísticas */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 className="text-lg font-semibold text-gray-900 mb-6">
          📊 Mi Rendimiento Hoy
        </h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {/* Para Cajeros y Vendedores */}
          {(['cajero', 'vendedor'].includes(auth?.user?.rol_principal || '')) && (
            <>
              <MetricCard
                title="💰 Mis Ventas"
                value={formatCurrency(miStats.ventas_hoy || 0)}
                emoji="💰"
                color="green"
                subtitle="vendidas hoy"
              />
              
              <MetricCard
                title="👥 Clientes"
                value={(miStats.clientes_atendidos || 0).toString()}
                emoji="👥"
                color="blue"
                subtitle="atendidos hoy"
              />
            </>
          )}
          
          {/* Para Almaceneros */}
          {(auth?.user?.rol_principal === 'almacenero') && (
            <MetricCard
              title="📦 Productos"
              value={(miStats.productos_vendidos || 0).toString()}
              emoji="📦"
              color="purple"
              subtitle="gestionados hoy"
            />
          )}

          {/* Estadística común */}
          <MetricCard
            title="⏰ Tiempo"
            value={miStats?.turno_inicio ? 
              `${Math.floor((new Date().getTime() - new Date(miStats.turno_inicio).getTime()) / (1000 * 60 * 60))}h` 
              : '0h'
            }
            emoji="⏰"
            color="indigo"
            subtitle="horas trabajadas"
          />
        </div>
      </div>

      {/* Tareas Pendientes */}
      {tareasPendientes.length > 0 && (
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">
            ✅ Tareas Pendientes
          </h2>
          <div className="space-y-3">
            {tareasPendientes.slice(0, 4).map((tarea) => (
              <div key={tarea.id} className="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                <div className="flex items-center space-x-3">
                  <span className="text-lg">
                    {tarea.tipo === 'venta' ? '🛒' : 
                     tarea.tipo === 'inventario' ? '📦' :
                     tarea.tipo === 'caja' ? '💳' : '👥'}
                  </span>
                  <div>
                    <p className="font-medium text-gray-900">{tarea.titulo}</p>
                    {tarea.tiempo_estimado && (
                      <p className="text-sm text-gray-600">⏱️ {tarea.tiempo_estimado}</p>
                    )}
                  </div>
                </div>
                <span className={`px-2 py-1 rounded-full text-xs font-medium border ${getUrgenciaColor(tarea.urgencia)}`}>
                  {tarea.urgencia === 'alta' ? '🔴 Urgente' :
                   tarea.urgencia === 'media' ? '🟡 Medio' : '🟢 Normal'}
                </span>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Accesos Rápidos */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div className="flex items-center justify-between mb-6">
          <h2 className="text-lg font-semibold text-gray-900">
            🚀 Mis Herramientas de Trabajo
          </h2>
          {/* Indicador de estado de desarrollo */}
          <div className="flex items-center space-x-2">
            <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
              ✅ Módulo Core Activo
            </span>
            <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
              🚧 Otros módulos en desarrollo
            </span>
          </div>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {accesos.map((acceso, index) => (
            <ActionCard
              key={index}
              title={acceso.titulo}
              description={acceso.disponible ? acceso.descripcion : `${acceso.descripcion} (Próximamente)`}
              emoji={acceso.emoji}
              href={acceso.disponible ? acceso.url : '#'}
              color={acceso.disponible ? 'blue' : 'indigo'}
              disabled={!acceso.disponible}
            />
          ))}
        </div>
        
        {/* Mensaje informativo para empleados */}
        {!['admin'].includes(auth?.user?.rol_principal || '') && (
          <div className="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <div className="flex items-start space-x-2">
              <span className="text-blue-600 text-lg">ℹ️</span>
              <div>
                <p className="text-sm text-blue-800 font-medium">
                  Desarrollo en progreso
                </p>
                <p className="text-sm text-blue-700 mt-1">
                  Las herramientas operativas estarán disponibles cuando el backend esté completo. 
                  Por ahora, los administradores pueden usar el <strong>Módulo Core</strong> para configurar la empresa.
                </p>
              </div>
            </div>
          </div>
        )}
      </div>

      {/* Información del Negocio */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 className="text-lg font-semibold text-gray-900 mb-6">
          🏪 Estado del Negocio
        </h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <MetricCard
            title="💰 Ventas Hoy"
            value={formatCurrency(stats?.ventasHoy || 0)}
            emoji="💰"
            color="green"
            subtitle="total del día"
          />
          
          <MetricCard
            title="👥 Clientes"
            value={(stats?.clientesActivos || 0).toString()}
            emoji="👥"
            color="blue"
            subtitle="activos"
          />
          
          <MetricCard
            title="📦 Stock"
            value={(stats?.productosStock || 0).toLocaleString()}
            emoji="📦"
            color="purple"
            subtitle="productos disponibles"
          />
        </div>
      </div>

      {/* Tips y Ayuda */}
      <div className="bg-blue-50 rounded-xl p-6 border border-blue-200">
        <div className="flex items-start space-x-3">
          <div className="flex-shrink-0">
            <CheckCircleIcon className="w-6 h-6 text-blue-600" />
          </div>
          <div>
            <h3 className="font-medium text-blue-900 mb-2">
              💡 Tip del día
            </h3>
            <p className="text-blue-800 text-sm">
              {(auth?.user?.rol_principal === 'cajero') && "Recuerda verificar el cambio antes de entregarlo al cliente. ¡Un buen servicio genera clientes felices!"}
              {(auth?.user?.rol_principal === 'vendedor') && "Preguntar por las necesidades del cliente te ayuda a recomendar los productos correctos y aumentar las ventas."}
              {(auth?.user?.rol_principal === 'almacenero') && "Revisa regularmente el stock mínimo de los productos más vendidos para evitar faltantes."}
              {(auth?.user?.rol_principal === 'admin') && "Revisar los reportes diarios te ayuda a identificar tendencias y oportunidades de mejora."}
              {(!['cajero', 'vendedor', 'almacenero', 'admin'].includes(auth?.user?.rol_principal || '')) && "¡Trabaja con dedicación y siempre busca maneras de mejorar el servicio al cliente!"}
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default EmployeeDashboard;
