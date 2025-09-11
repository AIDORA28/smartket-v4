import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import MetricCard from '../../../Components/core/shared/MetricCard';
import ActionCard from '../../../Components/core/shared/ActionCard';
import {
  UserGroupIcon,
  PlusIcon,
  PencilIcon,
  TrashIcon,
  MagnifyingGlassIcon,
  AdjustmentsHorizontalIcon,
  EnvelopeIcon,
  PhoneIcon,
  BuildingOfficeIcon,
  ShieldCheckIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  ClockIcon,
  KeyIcon,
  EyeIcon,
  PowerIcon,
  XMarkIcon
} from '@heroicons/react/24/outline';

interface UserManagementProps {
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
      limite_usuarios: number;
    };
  };
  usuarios: Array<{
    id: number;
    name: string;
    email: string;
    telefono?: string;
    rol_principal: string;
    estado: 'activo' | 'inactivo' | 'suspendido';
    sucursal_id?: number;
    sucursal_nombre?: string;
    fecha_creacion: string;
    ultimo_acceso?: string;
    email_verified_at?: string;
    permisos: Array<{
      modulo: string;
      acciones: string[];
    }>;
    estadisticas?: {
      ventas_realizadas: number;
      clientes_atendidos: number;
      dias_activo: number;
    };
  }>;
  sucursales: Array<{
    id: number;
    nombre: string;
  }>;
  roles: Array<{
    id: string;
    nombre: string;
    descripcion: string;
    permisos_default: string[];
  }>;
  resumen: {
    total_usuarios: number;
    usuarios_activos: number;
    usuarios_conectados_hoy: number;
    nuevos_este_mes: number;
  };
}

export default function UserManagement({ 
  auth, 
  empresa, 
  usuarios, 
  sucursales, 
  roles, 
  resumen 
}: UserManagementProps) {

  const [selectedUser, setSelectedUser] = useState<number | null>(null);
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [showUserDetails, setShowUserDetails] = useState<number | null>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [filterRole, setFilterRole] = useState('');
  const [filterStatus, setFilterStatus] = useState('');

  const { data, setData, post, put, delete: destroy, processing, errors, reset } = useForm({
    name: '',
    email: '',
    telefono: '',
    password: '',
    password_confirmation: '',
    rol_principal: '',
    sucursal_id: '',
    estado: 'activo' as 'activo' | 'inactivo' | 'suspendido',
    permisos: [] as string[]
  });

  const canCreateUser = () => {
    return resumen.total_usuarios < empresa.plan.limite_usuarios;
  };

  const getRoleColor = (role: string) => {
    switch (role.toLowerCase()) {
      case 'owner': return 'purple';
      case 'admin': return 'blue';
      case 'cajero': return 'green';
      case 'vendedor': return 'orange';
      case 'almacenero': return 'indigo';
      default: return 'gray';
    }
  };

  const getRoleIcon = (role: string) => {
    switch (role.toLowerCase()) {
      case 'owner': return '👑';
      case 'admin': return '👔';
      case 'cajero': return '💳';
      case 'vendedor': return '🛒';
      case 'almacenero': return '📦';
      default: return '👤';
    }
  };

  const getStatusColor = (estado: string) => {
    switch (estado) {
      case 'activo': return 'text-green-600 bg-green-100';
      case 'inactivo': return 'text-gray-600 bg-gray-100';
      case 'suspendido': return 'text-red-600 bg-red-100';
      default: return 'text-gray-600 bg-gray-100';
    }
  };

  const getStatusIcon = (estado: string) => {
    switch (estado) {
      case 'activo': return '✅';
      case 'inactivo': return '⏸️';
      case 'suspendido': return '🚫';
      default: return '❓';
    }
  };

  const filteredUsers = usuarios.filter(user => {
    const matchesSearch = user.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         user.email.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesRole = filterRole === '' || user.rol_principal === filterRole;
    const matchesStatus = filterStatus === '' || user.estado === filterStatus;
    
    return matchesSearch && matchesRole && matchesStatus;
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (selectedUser) {
      put(`/core/users/${selectedUser}`, {
        onSuccess: () => {
          setSelectedUser(null);
          setShowCreateForm(false);
          reset();
        }
      });
    } else {
      post('/core/users', {
        onSuccess: () => {
          setShowCreateForm(false);
          reset();
        }
      });
    }
  };

  const handleEdit = (user: typeof usuarios[0]) => {
    setData({
      name: user.name,
      email: user.email,
      telefono: user.telefono || '',
      password: '',
      password_confirmation: '',
      rol_principal: user.rol_principal,
      sucursal_id: user.sucursal_id?.toString() || '',
      estado: user.estado,
      permisos: user.permisos.flatMap(p => p.acciones)
    });
    setSelectedUser(user.id);
    setShowCreateForm(true);
  };

  const handleDelete = (userId: number) => {
    if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
      destroy(`/core/users/${userId}`);
    }
  };

  const handleToggleStatus = (userId: number, currentStatus: string) => {
    const newStatus = currentStatus === 'activo' ? 'inactivo' : 'activo';
    put(`/core/users/${userId}/status`, {
      data: { estado: newStatus }
    });
  };

  return (
    <AuthenticatedLayout>
      <Head title="Gestión de Usuarios - SmartKet" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          
          {/* Header */}
          <div className="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white mb-8">
            <div className="flex items-center justify-between">
              <div>
                <h1 className="text-3xl font-bold">
                  👥 Gestión de Usuarios
                </h1>
                <p className="text-blue-100 text-lg mt-2">
                  Administra el equipo de {empresa.nombre}
                </p>
              </div>
              <div className="text-right">
                <div className="bg-white bg-opacity-20 rounded-lg px-4 py-2 mb-2">
                  <p className="text-sm font-medium">
                    {resumen.total_usuarios}/{empresa.plan.limite_usuarios} Usuarios
                  </p>
                  <p className="text-blue-100 text-xs">Plan {empresa.plan.nombre}</p>
                </div>
                {canCreateUser() && (
                  <button
                    onClick={() => {
                      setShowCreateForm(true);
                      setSelectedUser(null);
                      reset();
                    }}
                    className="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-sm font-medium transition-colors"
                  >
                    <PlusIcon className="w-4 h-4 mr-2" />
                    Nuevo Usuario
                  </button>
                )}
              </div>
            </div>
          </div>

          {/* Summary Metrics */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <MetricCard
              title="👥 Total Usuarios"
              value={resumen.total_usuarios.toString()}
              emoji="👥"
              color="blue"
              subtitle={`${resumen.usuarios_activos} activos`}
            />
            
            <MetricCard
              title="🟢 Conectados Hoy"
              value={resumen.usuarios_conectados_hoy.toString()}
              emoji="🟢"
              color="green"
              subtitle="han iniciado sesión"
            />
            
            <MetricCard
              title="🆕 Nuevos Este Mes"
              value={resumen.nuevos_este_mes.toString()}
              emoji="🆕"
              color="purple"
              subtitle="usuarios registrados"
            />
            
            <MetricCard
              title="📊 Eficiencia"
              value={`${Math.round((resumen.usuarios_activos / resumen.total_usuarios) * 100)}%`}
              emoji="📊"
              color="orange"
              subtitle="usuarios activos"
            />
          </div>

          {/* Plan Limit Warning */}
          {!canCreateUser() && (
            <div className="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8">
              <div className="flex items-start space-x-3">
                <ExclamationTriangleIcon className="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" />
                <div>
                  <h3 className="font-medium text-amber-800">📊 Límite de Usuarios Alcanzado</h3>
                  <p className="text-amber-700 text-sm mt-1">
                    Has alcanzado el límite de {empresa.plan.limite_usuarios} usuarios de tu plan {empresa.plan.nombre}.
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

          {/* Filters */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-lg font-semibold text-gray-900">
                🔍 Filtros y Búsqueda
              </h2>
              <button
                onClick={() => {
                  setSearchTerm('');
                  setFilterRole('');
                  setFilterStatus('');
                }}
                className="text-sm text-gray-500 hover:text-gray-700"
              >
                Limpiar filtros
              </button>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Buscar usuario
                </label>
                <div className="relative">
                  <MagnifyingGlassIcon className="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                  <input
                    type="text"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    placeholder="Nombre o email..."
                    className="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Filtrar por rol
                </label>
                <select
                  value={filterRole}
                  onChange={(e) => setFilterRole(e.target.value)}
                  className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Todos los roles</option>
                  {roles.map(role => (
                    <option key={role.id} value={role.id}>
                      {getRoleIcon(role.id)} {role.nombre}
                    </option>
                  ))}
                </select>
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Filtrar por estado
                </label>
                <select
                  value={filterStatus}
                  onChange={(e) => setFilterStatus(e.target.value)}
                  className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Todos los estados</option>
                  <option value="activo">✅ Activo</option>
                  <option value="inactivo">⏸️ Inactivo</option>
                  <option value="suspendido">🚫 Suspendido</option>
                </select>
              </div>
            </div>
          </div>

          {/* Users Table */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div className="px-6 py-4 border-b border-gray-200">
              <h2 className="text-lg font-semibold text-gray-900">
                👥 Lista de Usuarios ({filteredUsers.length})
              </h2>
            </div>
            
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Usuario
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Rol y Sucursal
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Estado
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Último Acceso
                    </th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Acciones
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {filteredUsers.map((user) => (
                    <tr key={user.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="flex items-center">
                          <div className="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-medium">
                            {user.name.charAt(0).toUpperCase()}
                          </div>
                          <div className="ml-4">
                            <div className="text-sm font-medium text-gray-900">
                              {user.name}
                            </div>
                            <div className="text-sm text-gray-500 flex items-center">
                              <EnvelopeIcon className="w-4 h-4 mr-1" />
                              {user.email}
                            </div>
                            {user.telefono && (
                              <div className="text-sm text-gray-500 flex items-center">
                                <PhoneIcon className="w-4 h-4 mr-1" />
                                {user.telefono}
                              </div>
                            )}
                          </div>
                        </div>
                      </td>
                      
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm">
                          <div className="flex items-center mb-1">
                            <span className="mr-2">{getRoleIcon(user.rol_principal)}</span>
                            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${getRoleColor(user.rol_principal)}-100 text-${getRoleColor(user.rol_principal)}-800`}>
                              {user.rol_principal}
                            </span>
                          </div>
                          {user.sucursal_nombre && (
                            <div className="text-gray-500 flex items-center">
                              <BuildingOfficeIcon className="w-4 h-4 mr-1" />
                              {user.sucursal_nombre}
                            </div>
                          )}
                        </div>
                      </td>
                      
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(user.estado)}`}>
                          <span className="mr-1">{getStatusIcon(user.estado)}</span>
                          {user.estado}
                        </span>
                        {user.email_verified_at && (
                          <div className="text-xs text-green-600 flex items-center mt-1">
                            <CheckCircleIcon className="w-3 h-3 mr-1" />
                            Email verificado
                          </div>
                        )}
                      </td>
                      
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {user.ultimo_acceso ? (
                          <div className="flex items-center">
                            <ClockIcon className="w-4 h-4 mr-1" />
                            {new Date(user.ultimo_acceso).toLocaleDateString()}
                          </div>
                        ) : (
                          <span className="text-gray-400">Nunca</span>
                        )}
                      </td>
                      
                      <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div className="flex items-center justify-end space-x-2">
                          <button
                            onClick={() => setShowUserDetails(user.id)}
                            className="text-blue-600 hover:text-blue-900 p-1 rounded"
                            title="Ver detalles"
                          >
                            <EyeIcon className="w-4 h-4" />
                          </button>
                          
                          <button
                            onClick={() => handleEdit(user)}
                            className="text-indigo-600 hover:text-indigo-900 p-1 rounded"
                            title="Editar usuario"
                          >
                            <PencilIcon className="w-4 h-4" />
                          </button>
                          
                          {user.id !== auth.user.id && (
                            <>
                              <button
                                onClick={() => handleToggleStatus(user.id, user.estado)}
                                className={`p-1 rounded ${user.estado === 'activo' ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900'}`}
                                title={user.estado === 'activo' ? 'Desactivar' : 'Activar'}
                              >
                                <PowerIcon className="w-4 h-4" />
                              </button>
                              
                              <button
                                onClick={() => handleDelete(user.id)}
                                className="text-red-600 hover:text-red-900 p-1 rounded"
                                title="Eliminar usuario"
                              >
                                <TrashIcon className="w-4 h-4" />
                              </button>
                            </>
                          )}
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
              
              {filteredUsers.length === 0 && (
                <div className="text-center py-12">
                  <UserGroupIcon className="mx-auto h-12 w-12 text-gray-400" />
                  <h3 className="mt-2 text-sm font-medium text-gray-900">
                    No se encontraron usuarios
                  </h3>
                  <p className="mt-1 text-sm text-gray-500">
                    Intenta ajustar los filtros de búsqueda.
                  </p>
                </div>
              )}
            </div>
          </div>

          {/* Quick Actions */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">
              🚀 Acciones Rápidas
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <ActionCard
                title="Sucursales"
                description="Gestionar ubicaciones"
                emoji="🏪"
                href="/core/branches"
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

          {/* Create/Edit User Modal */}
          {showCreateForm && (
            <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
              <div className="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div className="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                  <h3 className="text-lg font-semibold text-gray-900">
                    {selectedUser ? '✏️ Editar Usuario' : '➕ Nuevo Usuario'}
                  </h3>
                  <button
                    onClick={() => {
                      setShowCreateForm(false);
                      setSelectedUser(null);
                      reset();
                    }}
                    className="text-gray-400 hover:text-gray-600"
                  >
                    <XMarkIcon className="w-6 h-6" />
                  </button>
                </div>
                
                <form onSubmit={handleSubmit} className="p-6 space-y-6">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">
                        Nombre completo *
                      </label>
                      <input
                        type="text"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                      />
                      {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
                    </div>
                    
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">
                        Email *
                      </label>
                      <input
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                      />
                      {errors.email && <p className="text-red-500 text-sm mt-1">{errors.email}</p>}
                    </div>
                    
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono
                      </label>
                      <input
                        type="tel"
                        value={data.telefono}
                        onChange={(e) => setData('telefono', e.target.value)}
                        className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      />
                      {errors.telefono && <p className="text-red-500 text-sm mt-1">{errors.telefono}</p>}
                    </div>
                    
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">
                        Rol *
                      </label>
                      <select
                        value={data.rol_principal}
                        onChange={(e) => setData('rol_principal', e.target.value)}
                        className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                      >
                        <option value="">Seleccionar rol</option>
                        {roles.map(role => (
                          <option key={role.id} value={role.id}>
                            {getRoleIcon(role.id)} {role.nombre}
                          </option>
                        ))}
                      </select>
                      {errors.rol_principal && <p className="text-red-500 text-sm mt-1">{errors.rol_principal}</p>}
                    </div>
                    
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">
                        Sucursal
                      </label>
                      <select
                        value={data.sucursal_id}
                        onChange={(e) => setData('sucursal_id', e.target.value)}
                        className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      >
                        <option value="">Sin sucursal asignada</option>
                        {sucursales.map(sucursal => (
                          <option key={sucursal.id} value={sucursal.id}>
                            {sucursal.nombre}
                          </option>
                        ))}
                      </select>
                      {errors.sucursal_id && <p className="text-red-500 text-sm mt-1">{errors.sucursal_id}</p>}
                    </div>
                    
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">
                        Estado
                      </label>
                      <select
                        value={data.estado}
                        onChange={(e) => setData('estado', e.target.value as 'activo' | 'inactivo' | 'suspendido')}
                        className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      >
                        <option value="activo">✅ Activo</option>
                        <option value="inactivo">⏸️ Inactivo</option>
                        <option value="suspendido">🚫 Suspendido</option>
                      </select>
                      {errors.estado && <p className="text-red-500 text-sm mt-1">{errors.estado}</p>}
                    </div>
                  </div>
                  
                  {!selectedUser && (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          Contraseña *
                        </label>
                        <input
                          type="password"
                          value={data.password}
                          onChange={(e) => setData('password', e.target.value)}
                          className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          required={!selectedUser}
                        />
                        {errors.password && <p className="text-red-500 text-sm mt-1">{errors.password}</p>}
                      </div>
                      
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          Confirmar contraseña *
                        </label>
                        <input
                          type="password"
                          value={data.password_confirmation}
                          onChange={(e) => setData('password_confirmation', e.target.value)}
                          className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          required={!selectedUser}
                        />
                        {errors.password_confirmation && <p className="text-red-500 text-sm mt-1">{errors.password_confirmation}</p>}
                      </div>
                    </div>
                  )}
                  
                  <div className="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button
                      type="button"
                      onClick={() => {
                        setShowCreateForm(false);
                        setSelectedUser(null);
                        reset();
                      }}
                      className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                      Cancelar
                    </button>
                    <button
                      type="submit"
                      disabled={processing}
                      className="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 disabled:opacity-50"
                    >
                      {processing ? 'Guardando...' : (selectedUser ? 'Actualizar' : 'Crear Usuario')}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          )}

        </div>
      </div>
    </AuthenticatedLayout>
  );
}
