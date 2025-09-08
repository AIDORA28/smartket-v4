import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout';

interface ClientsPageProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
      rol_principal: string;
    };
  };
  clientes?: any[];
  empresa: {
    id: number;
    nombre: string;
  };
  error?: string;
}

export default function Clients({ 
  auth, 
  clientes = [], 
  empresa,
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
            👤 Gestión de Clientes - {empresa.nombre}
          </h2>
        </div>
      }
    >
      <Head title="Clientes" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          
          {/* Estadística */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Total Clientes</p>
                  <p className="text-2xl font-bold text-gray-900">{clientes.length}</p>
                </div>
                <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                  <span className="text-blue-600 text-xl">👤</span>
                </div>
              </div>
            </div>

            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Activos</p>
                  <p className="text-2xl font-bold text-green-900">0</p>
                </div>
                <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                  <span className="text-green-600 text-xl">✅</span>
                </div>
              </div>
            </div>

            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Nuevos este mes</p>
                  <p className="text-2xl font-bold text-purple-900">0</p>
                </div>
                <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                  <span className="text-purple-600 text-xl">📈</span>
                </div>
              </div>
            </div>
          </div>

          {/* Contenido Principal */}
          <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-medium text-gray-900 mb-4">
              Módulo de Clientes - Versión Simplificada
            </h3>
            
            <div className="space-y-4">
              <div className="border-l-4 border-blue-500 bg-blue-50 p-4">
                <div className="flex">
                  <div>
                    <h4 className="text-blue-800 font-medium">✅ Página funcionando</h4>
                    <p className="text-blue-700 text-sm mt-1">
                      El módulo de clientes está cargando correctamente
                    </p>
                  </div>
                </div>
              </div>

              <div className="border-l-4 border-yellow-500 bg-yellow-50 p-4">
                <div className="flex">
                  <div>
                    <h4 className="text-yellow-800 font-medium">🚧 En desarrollo:</h4>
                    <p className="text-yellow-700 text-sm mt-1">
                      Las funcionalidades completas de clientes se agregarán después de confirmar que esta versión simplificada funciona
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
