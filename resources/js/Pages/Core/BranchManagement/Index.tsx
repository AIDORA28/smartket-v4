import React, { useState } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { BranchManagementIndexProps } from '@/Types/core';
import { route } from 'ziggy-js';
import {
  PlusIcon,
  BuildingOfficeIcon,
  MapPinIcon,
  ChartBarIcon,
  Cog6ToothIcon,
  TrashIcon,
  PencilIcon,
  ArrowPathIcon
} from '@heroicons/react/24/outline';

const BranchManagementIndex: React.FC = () => {
  const props = usePage().props as any;
  const { empresa, sucursales, can } = props;
  const [showDeleteModal, setShowDeleteModal] = useState<number | null>(null);

  const getStatusBadge = (activa: boolean) => {
    return activa ? (
      <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
        Activa
      </span>
    ) : (
      <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
        Inactiva
      </span>
    );
  };

  const getPerformanceIndicator = (performance: any) => {
    if (!performance) return null;
    
    const growth = performance.crecimiento_vs_mes_anterior || 0;
    const isPositive = growth >= 0;
    
    return (
      <div className={`flex items-center text-sm ${isPositive ? 'text-green-600' : 'text-red-600'}`}>
        {isPositive ? '↗' : '↘'} {Math.abs(growth).toFixed(1)}%
      </div>
    );
  };

  return (
    <AuthenticatedLayout>
      <Head title="Gestión de Sucursales" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          {/* Header */}
          <div className="mb-8">
            <div className="flex items-center justify-between">
              <div>
                <h1 className="text-3xl font-bold text-gray-900">
                  Gestión de Sucursales
                </h1>
                <p className="mt-2 text-sm text-gray-600">
                  Administra las sucursales de {empresa.nombre}
                </p>
              </div>
              <div className="flex items-center space-x-3">
                {can.create_branch && (
                  <Link
                    href={route('core.branches.create')}
                    className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    <PlusIcon className="w-4 h-4 mr-2" />
                    Nueva Sucursal
                  </Link>
                )}
                <button
                  onClick={() => window.location.reload()}
                  className="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <ArrowPathIcon className="w-4 h-4 mr-2" />
                  Actualizar
                </button>
              </div>
            </div>
          </div>

          {/* Stats Cards */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <BuildingOfficeIcon className="w-8 h-8 text-blue-600" />
                </div>
                <div className="ml-4">
                  <p className="text-sm font-medium text-gray-500">Total Sucursales</p>
                  <p className="text-2xl font-semibold text-gray-900">{sucursales.total}</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <ChartBarIcon className="w-8 h-8 text-green-600" />
                </div>
                <div className="ml-4">
                  <p className="text-sm font-medium text-gray-500">Sucursales Activas</p>
                  <p className="text-2xl font-semibold text-gray-900">
                    {sucursales.data.filter((s: any) => s.activa).length}
                  </p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <MapPinIcon className="w-8 h-8 text-purple-600" />
                </div>
                <div className="ml-4">
                  <p className="text-sm font-medium text-gray-500">Mejor Rendimiento</p>
                  <p className="text-lg font-semibold text-gray-900">
                    {sucursales.data.find((s: any) => s.performance?.ranking_empresa === 1)?.nombre || 'N/A'}
                  </p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <Cog6ToothIcon className="w-8 h-8 text-orange-600" />
                </div>
                <div className="ml-4">
                  <p className="text-sm font-medium text-gray-500">Con Configuración</p>
                  <p className="text-2xl font-semibold text-gray-900">
                    {sucursales.data.filter((s: any) => s.settings).length}
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* Branches List */}
          <div className="bg-white shadow-sm rounded-lg border border-gray-200">
            <div className="px-6 py-4 border-b border-gray-200">
              <h2 className="text-lg font-medium text-gray-900">Listado de Sucursales</h2>
            </div>

            {sucursales.data.length === 0 ? (
              <div className="text-center py-12">
                <BuildingOfficeIcon className="mx-auto h-12 w-12 text-gray-400" />
                <h3 className="mt-2 text-sm font-medium text-gray-900">No hay sucursales</h3>
                <p className="mt-1 text-sm text-gray-500">
                  Comienza creando la primera sucursal de tu empresa.
                </p>
                {can.create_branch && (
                  <div className="mt-6">
                    <Link
                      href={route('core.branches.create')}
                      className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                    >
                      <PlusIcon className="w-4 h-4 mr-2" />
                      Nueva Sucursal
                    </Link>
                  </div>
                )}
              </div>
            ) : (
              <div className="overflow-hidden">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Sucursal
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Dirección
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Rendimiento
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Configuración
                      </th>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {sucursales.data.map((sucursal: any) => (
                      <tr key={sucursal.id} className="hover:bg-gray-50">
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="flex items-center">
                            <div className="flex-shrink-0 h-10 w-10">
                              <div className="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                <BuildingOfficeIcon className="h-6 w-6 text-blue-600" />
                              </div>
                            </div>
                            <div className="ml-4">
                              <div className="text-sm font-medium text-gray-900">
                                {sucursal.nombre}
                              </div>
                              {sucursal.codigo_interno && (
                                <div className="text-sm text-gray-500">
                                  Código: {sucursal.codigo_interno}
                                </div>
                              )}
                            </div>
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-900">
                            {sucursal.direccion || '-'}
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          {getStatusBadge(sucursal.activa)}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          {sucursal.performance ? (
                            <div className="space-y-1">
                              <div className="text-sm font-medium text-gray-900">
                                S/ {sucursal.performance.ventas_mes?.toLocaleString() || '0'}
                              </div>
                              {getPerformanceIndicator(sucursal.performance)}
                            </div>
                          ) : (
                            <span className="text-sm text-gray-400">Sin datos</span>
                          )}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          {sucursal.settings ? (
                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                              Configurada
                            </span>
                          ) : (
                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                              Pendiente
                            </span>
                          )}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                          <div className="flex items-center justify-end space-x-2">
                            <Link
                              href={route('core.branches.show', sucursal.id)}
                              className="text-blue-600 hover:text-blue-900 p-1 rounded"
                              title="Ver detalles"
                            >
                              <ChartBarIcon className="w-4 h-4" />
                            </Link>
                            <Link
                              href={route('core.branches.settings', sucursal.id)}
                              className="text-purple-600 hover:text-purple-900 p-1 rounded"
                              title="Configurar"
                            >
                              <Cog6ToothIcon className="w-4 h-4" />
                            </Link>
                            {can.edit_branch && (
                              <Link
                                href={route('core.branches.edit', sucursal.id)}
                                className="text-green-600 hover:text-green-900 p-1 rounded"
                                title="Editar"
                              >
                                <PencilIcon className="w-4 h-4" />
                              </Link>
                            )}
                            {can.delete_branch && (
                              <button
                                onClick={() => setShowDeleteModal(sucursal.id)}
                                className="text-red-600 hover:text-red-900 p-1 rounded"
                                title="Eliminar"
                              >
                                <TrashIcon className="w-4 h-4" />
                              </button>
                            )}
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>

          {/* Quick Actions */}
          <div className="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <Link
              href={route('core.branches.transfers.index')}
              className="block p-6 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200"
            >
              <div className="flex items-center">
                <ArrowPathIcon className="w-8 h-8 text-green-600" />
                <div className="ml-4">
                  <h3 className="text-lg font-medium text-gray-900">
                    Transferencias
                  </h3>
                  <p className="text-sm text-gray-600">
                    Gestionar transferencias entre sucursales
                  </p>
                </div>
              </div>
            </Link>

            <Link
              href={route('core.company.analytics.index')}
              className="block p-6 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200"
            >
              <div className="flex items-center">
                <ChartBarIcon className="w-8 h-8 text-blue-600" />
                <div className="ml-4">
                  <h3 className="text-lg font-medium text-gray-900">
                    Analytics Globales
                  </h3>
                  <p className="text-sm text-gray-600">
                    Ver métricas consolidadas
                  </p>
                </div>
              </div>
            </Link>

            <button
              onClick={() => {/* TODO: Implement bulk actions */}}
              className="block w-full p-6 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200 text-left"
            >
              <div className="flex items-center">
                <Cog6ToothIcon className="w-8 h-8 text-purple-600" />
                <div className="ml-4">
                  <h3 className="text-lg font-medium text-gray-900">
                    Configuración Masiva
                  </h3>
                  <p className="text-sm text-gray-600">
                    Aplicar configuraciones a múltiples sucursales
                  </p>
                </div>
              </div>
            </button>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
};

export default BranchManagementIndex;
