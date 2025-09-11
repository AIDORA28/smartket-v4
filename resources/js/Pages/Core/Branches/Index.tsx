import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import MetricCard from '../../../Components/core/shared/MetricCard';
import ActionCard from '../../../Components/core/shared/ActionCard';
import {
  BuildingOffice2Icon,
  MapPinIcon,
  PhoneIcon,
  EnvelopeIcon,
  UsersIcon,
  ChartBarIcon,
  CogIcon,
  PlusIcon,
  PencilIcon,
  TrashIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  ClockIcon
} from '@heroicons/react/24/outline';

interface BranchManagementProps {
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
    plan: {
      nombre: string;
      limite_sucursales: number;
    };
  };
  sucursales: Array<{
    id: number;
    nombre: string;
    direccion: string;
    telefono?: string;
    email?: string;
    responsable_nombre?: string;
    responsable_email?: string;
    estado: 'activa' | 'inactiva' | 'mantenimiento';
    fecha_creacion: string;
    usuarios_count: number;
    ventas_hoy: number;
    ventas_mes: number;
    productos_count: number;
    clientes_count: number;
    configuracion: {
      horario_apertura?: string;
      horario_cierre?: string;
      zona_horaria: string;
      moneda: string;
    };
  }>;
  resumen: {
    total_sucursales: number;
    sucursales_activas: number;
    total_usuarios: number;
    ventas_totales_hoy: number;
    ventas_totales_mes: number;
  };
}

export default function BranchManagement({ 
  auth, 
  empresa, 
  sucursales, 
  resumen 
}: BranchManagementProps) {

  const [selectedBranch, setSelectedBranch] = useState<number | null>(null);
  const [showCreateForm, setShowCreateForm] = useState(false);

  const { data, setData, post, put, delete: destroy, processing, errors, reset } = useForm({
    nombre: '',
    direccion: '',
    telefono: '',
    email: '',
    responsable_nombre: '',
    responsable_email: '',
    horario_apertura: '08:00',
    horario_cierre: '18:00',
    zona_horaria: 'America/Lima'
  });

  const formatCurrency = (amount: number = 0) => {
    return new Intl.NumberFormat('es-PE', {
      style: 'currency',
      currency: 'PEN',
      minimumFractionDigits: 0
    }).format(amount);
  };

  const getStatusColor = (estado: string) => {
    switch (estado) {
      case 'activa': return 'green';
      case 'inactiva': return 'red';
      case 'mantenimiento': return 'orange';
      default: return 'blue';
    }
  };

  const getStatusIcon = (estado: string) => {
    switch (estado) {
      case 'activa': return '✅';
      case 'inactiva': return '❌';
      case 'mantenimiento': return '🔧';
      default: return '❓';
    }
  };

  const canCreateBranch = () => {
    return resumen.total_sucursales < empresa.plan.limite_sucursales;
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (selectedBranch) {
      put(`/core/branches/${selectedBranch}`, {
        onSuccess: () => {
          setSelectedBranch(null);
          reset();
        }
      });
    } else {
      post('/core/branches', {
        onSuccess: () => {
          setShowCreateForm(false);
          reset();
        }
      });
    }
  };

  const handleEdit = (branch: typeof sucursales[0]) => {
    setData({
      nombre: branch.nombre,
      direccion: branch.direccion,
      telefono: branch.telefono || '',
      email: branch.email || '',
      responsable_nombre: branch.responsable_nombre || '',
      responsable_email: branch.responsable_email || '',
      horario_apertura: branch.configuracion.horario_apertura || '08:00',
      horario_cierre: branch.configuracion.horario_cierre || '18:00',
      zona_horaria: branch.configuracion.zona_horaria
    });
    setSelectedBranch(branch.id);
    setShowCreateForm(true);
  };

  const handleDelete = (branchId: number) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta sucursal?')) {
      destroy(`/core/branches/${branchId}`);
    }
  };

  return (
    <AuthenticatedLayout>
      <Head title="Gestión de Sucursales - SmartKet" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          
          {/* Header */}
          <div className="bg-gradient-to-r from-green-600 to-blue-600 rounded-2xl p-6 text-white mb-8">
            <div className="flex items-center justify-between">
              <div>
                <h1 className="text-3xl font-bold">
                  🏪 Gestión de Sucursales
                </h1>
                <p className="text-green-100 text-lg mt-2">
                  Administra todas las ubicaciones de {empresa.nombre}
                </p>
              </div>
              <div className="text-right">
                <div className="bg-white bg-opacity-20 rounded-lg px-4 py-2 mb-2">
                  <p className="text-sm font-medium">
                    {resumen.total_sucursales}/{empresa.plan.limite_sucursales} Sucursales
                  </p>
                  <p className="text-green-100 text-xs">Plan {empresa.plan.nombre}</p>
                </div>
                {canCreateBranch() && (
                  <button
                    onClick={() => {
                      setShowCreateForm(true);
                      setSelectedBranch(null);
                      reset();
                    }}
                    className="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-sm font-medium transition-colors"
                  >
                    <PlusIcon className="w-4 h-4 mr-2" />
                    Nueva Sucursal
                  </button>
                )}
              </div>
            </div>
          </div>

          {/* Summary Metrics */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <MetricCard
              title="🏪 Total Sucursales"
              value={resumen.total_sucursales.toString()}
              emoji="🏪"
              color="blue"
              subtitle={`${resumen.sucursales_activas} activas`}
            />
            
            <MetricCard
              title="👥 Total Usuarios"
              value={resumen.total_usuarios.toString()}
              emoji="👥"
              color="green"
              subtitle="en todas las sucursales"
            />
            
            <MetricCard
              title="💰 Ventas Hoy"
              value={formatCurrency(resumen.ventas_totales_hoy)}
              emoji="💰"
              color="purple"
              subtitle="todas las sucursales"
            />
            
            <MetricCard
              title="📊 Ventas Mes"
              value={formatCurrency(resumen.ventas_totales_mes)}
              emoji="📊"
              color="orange"
              trend={{ value: 15, isPositive: true }}
              subtitle="vs mes anterior"
            />
          </div>

          {/* Plan Limit Warning */}
          {!canCreateBranch() && (
            <div className="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8">
              <div className="flex items-start space-x-3">
                <ExclamationTriangleIcon className="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" />
                <div>
                  <h3 className="font-medium text-amber-800">📊 Límite de Sucursales Alcanzado</h3>
                  <p className="text-amber-700 text-sm mt-1">
                    Has alcanzado el límite de {empresa.plan.limite_sucursales} sucursales de tu plan {empresa.plan.nombre}.
                  </p>
                  <div className="mt-3">
                    <Link
                      href="/core/company/settings?tab=plan"
                      className="inline-flex items-center text-amber-800 font-medium hover:text-amber-900"
                    >
                      ⬆️ Mejorar Plan →
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* Create/Edit Form */}
          {showCreateForm && (
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
              <div className="flex items-center justify-between mb-6">
                <h2 className="text-lg font-semibold text-gray-900">
                  {selectedBranch ? '✏️ Editar Sucursal' : '🏪 Nueva Sucursal'}
                </h2>
                <button
                  onClick={() => {
                    setShowCreateForm(false);
                    setSelectedBranch(null);
                    reset();
                  }}
                  className="text-gray-400 hover:text-gray-600"
                >
                  ✕
                </button>
              </div>

              <form onSubmit={handleSubmit} className="space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Nombre de la Sucursal *
                    </label>
                    <input
                      type="text"
                      value={data.nombre}
                      onChange={(e) => setData('nombre', e.target.value)}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="Ej: Sucursal Centro"
                      required
                    />
                    {errors.nombre && (
                      <p className="mt-1 text-sm text-red-600">{errors.nombre}</p>
                    )}
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Teléfono
                    </label>
                    <input
                      type="tel"
                      value={data.telefono}
                      onChange={(e) => setData('telefono', e.target.value)}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="+51 999 999 999"
                    />
                  </div>

                  <div className="md:col-span-2">
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Dirección *
                    </label>
                    <textarea
                      value={data.direccion}
                      onChange={(e) => setData('direccion', e.target.value)}
                      rows={3}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="Dirección completa de la sucursal"
                      required
                    />
                    {errors.direccion && (
                      <p className="mt-1 text-sm text-red-600">{errors.direccion}</p>
                    )}
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Email
                    </label>
                    <input
                      type="email"
                      value={data.email}
                      onChange={(e) => setData('email', e.target.value)}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="sucursal@empresa.com"
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Responsable
                    </label>
                    <input
                      type="text"
                      value={data.responsable_nombre}
                      onChange={(e) => setData('responsable_nombre', e.target.value)}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="Nombre del responsable"
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Email del Responsable
                    </label>
                    <input
                      type="email"
                      value={data.responsable_email}
                      onChange={(e) => setData('responsable_email', e.target.value)}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="responsable@empresa.com"
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Zona Horaria
                    </label>
                    <select
                      value={data.zona_horaria}
                      onChange={(e) => setData('zona_horaria', e.target.value)}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                      <option value="America/Lima">🇵🇪 Lima (UTC-5)</option>
                      <option value="America/New_York">🇺🇸 Nueva York (UTC-5)</option>
                      <option value="Europe/Madrid">🇪🇸 Madrid (UTC+1)</option>
                    </select>
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">
                        Hora Apertura
                      </label>
                      <input
                        type="time"
                        value={data.horario_apertura}
                        onChange={(e) => setData('horario_apertura', e.target.value)}
                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">
                        Hora Cierre
                      </label>
                      <input
                        type="time"
                        value={data.horario_cierre}
                        onChange={(e) => setData('horario_cierre', e.target.value)}
                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      />
                    </div>
                  </div>
                </div>

                <div className="flex justify-end space-x-3">
                  <button
                    type="button"
                    onClick={() => {
                      setShowCreateForm(false);
                      setSelectedBranch(null);
                      reset();
                    }}
                    className="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    disabled={processing}
                    className="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-lg transition-colors"
                  >
                    {processing ? (
                      <>
                        <svg className="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                          <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Guardando...
                      </>
                    ) : (
                      <>
                        {selectedBranch ? (
                          <>
                            <PencilIcon className="w-4 h-4 mr-2" />
                            Actualizar Sucursal
                          </>
                        ) : (
                          <>
                            <PlusIcon className="w-4 h-4 mr-2" />
                            Crear Sucursal
                          </>
                        )}
                      </>
                    )}
                  </button>
                </div>
              </form>
            </div>
          )}

          {/* Branches List */}
          <div className="space-y-6">
            {sucursales.map((sucursal) => (
              <div key={sucursal.id} className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div className="p-6">
                  <div className="flex items-start justify-between">
                    <div className="flex-1">
                      <div className="flex items-center space-x-3 mb-2">
                        <h3 className="text-xl font-semibold text-gray-900">{sucursal.nombre}</h3>
                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                          sucursal.estado === 'activa' ? 'bg-green-100 text-green-800' :
                          sucursal.estado === 'inactiva' ? 'bg-red-100 text-red-800' :
                          'bg-orange-100 text-orange-800'
                        }`}>
                          {getStatusIcon(sucursal.estado)} {sucursal.estado.charAt(0).toUpperCase() + sucursal.estado.slice(1)}
                        </span>
                      </div>
                      
                      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                        <div className="flex items-center text-gray-600">
                          <MapPinIcon className="w-4 h-4 mr-2" />
                          <span className="text-sm">{sucursal.direccion}</span>
                        </div>
                        {sucursal.telefono && (
                          <div className="flex items-center text-gray-600">
                            <PhoneIcon className="w-4 h-4 mr-2" />
                            <span className="text-sm">{sucursal.telefono}</span>
                          </div>
                        )}
                        {sucursal.email && (
                          <div className="flex items-center text-gray-600">
                            <EnvelopeIcon className="w-4 h-4 mr-2" />
                            <span className="text-sm">{sucursal.email}</span>
                          </div>
                        )}
                        {sucursal.responsable_nombre && (
                          <div className="flex items-center text-gray-600">
                            <UsersIcon className="w-4 h-4 mr-2" />
                            <span className="text-sm">{sucursal.responsable_nombre}</span>
                          </div>
                        )}
                        <div className="flex items-center text-gray-600">
                          <ClockIcon className="w-4 h-4 mr-2" />
                          <span className="text-sm">
                            {sucursal.configuracion.horario_apertura} - {sucursal.configuracion.horario_cierre}
                          </span>
                        </div>
                        <div className="flex items-center text-gray-600">
                          <BuildingOffice2Icon className="w-4 h-4 mr-2" />
                          <span className="text-sm">
                            Creada {new Date(sucursal.fecha_creacion).toLocaleDateString('es-PE')}
                          </span>
                        </div>
                      </div>

                      {/* Metrics */}
                      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div className="text-center">
                          <p className="text-lg font-bold text-gray-900">{sucursal.usuarios_count}</p>
                          <p className="text-sm text-gray-600">👥 Usuarios</p>
                        </div>
                        <div className="text-center">
                          <p className="text-lg font-bold text-gray-900">{formatCurrency(sucursal.ventas_hoy)}</p>
                          <p className="text-sm text-gray-600">💰 Ventas Hoy</p>
                        </div>
                        <div className="text-center">
                          <p className="text-lg font-bold text-gray-900">{formatCurrency(sucursal.ventas_mes)}</p>
                          <p className="text-sm text-gray-600">📊 Ventas Mes</p>
                        </div>
                        <div className="text-center">
                          <p className="text-lg font-bold text-gray-900">{sucursal.productos_count}</p>
                          <p className="text-sm text-gray-600">📦 Productos</p>
                        </div>
                      </div>
                    </div>

                    <div className="flex space-x-2 ml-4">
                      <button
                        onClick={() => handleEdit(sucursal)}
                        className="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                        title="Editar sucursal"
                      >
                        <PencilIcon className="w-5 h-5" />
                      </button>
                      <Link
                        href={`/core/branches/${sucursal.id}/analytics`}
                        className="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                        title="Ver analytics"
                      >
                        <ChartBarIcon className="w-5 h-5" />
                      </Link>
                      <button
                        onClick={() => handleDelete(sucursal.id)}
                        className="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                        title="Eliminar sucursal"
                        disabled={sucursales.length <= 1}
                      >
                        <TrashIcon className="w-5 h-5" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>

          {/* Empty State */}
          {sucursales.length === 0 && (
            <div className="text-center py-12">
              <BuildingOffice2Icon className="w-16 h-16 text-gray-400 mx-auto mb-4" />
              <h3 className="text-lg font-medium text-gray-900 mb-2">
                No hay sucursales registradas
              </h3>
              <p className="text-gray-600 mb-6">
                Crea tu primera sucursal para comenzar a gestionar tu negocio.
              </p>
              {canCreateBranch() && (
                <button
                  onClick={() => {
                    setShowCreateForm(true);
                    setSelectedBranch(null);
                    reset();
                  }}
                  className="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
                >
                  <PlusIcon className="w-5 h-5 mr-2" />
                  Crear Primera Sucursal
                </button>
              )}
            </div>
          )}

          {/* Quick Actions */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-8">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">
              🚀 Acciones Rápidas
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <ActionCard
                title="Usuarios"
                description="Gestionar empleados"
                emoji="👥"
                href="/core/users"
                color="blue"
              />
              
              <ActionCard
                title="Analytics"
                description="Ver rendimiento"
                emoji="📊"
                href="/core/analytics"
                color="green"
              />
              
              <ActionCard
                title="Configuración"
                description="Ajustes empresa"
                emoji="⚙️"
                href="/core/company/settings"
                color="purple"
              />
              
              <ActionCard
                title="Dashboard"
                description="Vista general"
                emoji="🏠"
                href="/dashboard"
                color="orange"
              />
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
