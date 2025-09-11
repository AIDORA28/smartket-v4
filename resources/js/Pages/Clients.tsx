import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout';
// Modular Components
import MetricCard from '@/Components/core/shared/MetricCard';
import ActionCard from '@/Components/core/shared/ActionCard';

interface ClientData {
  id: number;
  nombre: string;
  email: string | null;
  telefono: string | null;
  ruc: string | null;
  credito_limite: number;
  credito_usado: number;
  activo: boolean;
  total_compras: number;
}

interface ClientsPageProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
      rol_principal: string;
    };
  };
  clients: {
    data: ClientData[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  stats: {
    total_clientes: number;
    clientes_activos: number;
    con_credito: number;
    credito_pendiente: number;
  };
  filters: {
    search: string;
    status: string;
    credit: string;
  };
  error?: string;
}

export default function Clients({ 
  auth, 
  clients, 
  stats,
  filters,
  error 
}: ClientsPageProps) {
  
  if (error) {
    return (
      <AuthenticatedLayout>
        <Head title="Clientes - Error" />
        <div className="py-6">
          <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div className="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
              <h2 className="text-lg font-medium text-red-900 mb-2">Error de configuración</h2>
              <p className="text-red-700">{error}</p>
            </div>
          </div>
        </div>
      </AuthenticatedLayout>
    );
  }

  return (
    <AuthenticatedLayout
      header={
        <div className="flex justify-between items-center">
          <h2 className="font-semibold text-xl text-gray-800 leading-tight">
            👤 Gestión de Clientes
          </h2>
        </div>
      }
    >
      <Head title="Clientes" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          
          {/* Estadísticas Mejoradas con MetricCard */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <MetricCard
              title="👤 Total Clientes"
              value={stats?.total_clientes?.toString() || '0'}
              emoji="👤"
              color="blue"
              subtitle="clientes registrados"
            />
            
            <MetricCard
              title="✅ Clientes Activos"
              value={stats?.clientes_activos?.toString() || '0'}
              emoji="✅"
              color="green"
              subtitle="activos en sistema"
            />
            
            <MetricCard
              title="💳 Con Crédito"
              value={stats?.con_credito?.toString() || '0'}
              emoji="💳"
              color="purple"
              subtitle="clientes con crédito"
            />
            
            <MetricCard
              title="💰 Crédito Pendiente"
              value={`S/. ${stats?.credito_pendiente?.toFixed(2) || '0.00'}`}
              emoji="💰"
              color="red"
              subtitle="por cobrar"
            />
          </div>

          {/* Lista de Clientes */}
          <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-medium text-gray-900 mb-4">
              Lista de Clientes ({clients?.total || 0} total)
            </h3>
            
            <div className="space-y-4">
              <div className="border-l-4 border-green-500 bg-green-50 p-4">
                <div className="flex">
                  <div>
                    <h4 className="text-green-800 font-medium">✅ Módulo funcionando</h4>
                    <p className="text-green-700 text-sm mt-1">
                      El módulo de clientes está cargando correctamente con datos de prueba
                    </p>
                  </div>
                </div>
              </div>

              {/* Tabla simple de clientes */}
              {clients?.data && clients.data.length > 0 ? (
                <div className="overflow-x-auto">
                  <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                      <tr>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Cliente
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Contacto
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Crédito
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Estado
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Total Compras
                        </th>
                      </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                      {clients.data.map((client) => (
                        <tr key={client.id} className="hover:bg-gray-50">
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div>
                              <div className="text-sm font-medium text-gray-900">{client.nombre}</div>
                              <div className="text-sm text-gray-500">RUC: {client.ruc || 'No registrado'}</div>
                            </div>
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div className="text-sm text-gray-900">{client.email || 'Sin email'}</div>
                            <div className="text-sm text-gray-500">{client.telefono || 'Sin teléfono'}</div>
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div className="text-sm text-gray-900">
                              Límite: S/. {client.credito_limite.toFixed(2)}
                            </div>
                            <div className="text-sm text-gray-500">
                              Usado: S/. {client.credito_usado.toFixed(2)}
                            </div>
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap">
                            <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                              client.activo 
                                ? 'bg-green-100 text-green-800' 
                                : 'bg-red-100 text-red-800'
                            }`}>
                              {client.activo ? 'Activo' : 'Inactivo'}
                            </span>
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            S/. {client.total_compras.toFixed(2)}
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ) : (
                <div className="text-center py-8">
                  <p className="text-gray-500">No hay clientes registrados aún</p>
                </div>
              )}

              <div className="border-l-4 border-yellow-500 bg-yellow-50 p-4 mt-4">
                <div className="flex">
                  <div>
                    <h4 className="text-yellow-800 font-medium">🚧 Próximas funcionalidades:</h4>
                    <p className="text-yellow-700 text-sm mt-1">
                      • Crear nuevo cliente • Editar clientes • Filtros avanzados • Exportar datos
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
