import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { clsx } from 'clsx';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout';
import { Button } from '../Components/ui/Button';
import { Card, CardHeader, CardBody } from '../Components/ui/Card';
import {
  PlusIcon,
  MagnifyingGlassIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  UserIcon,
  PhoneIcon,
  EnvelopeIcon,
  CreditCardIcon,
  AdjustmentsHorizontalIcon,
  CloudArrowDownIcon
} from '@heroicons/react/24/outline';

interface Client {
  id: number;
  nombre: string;
  email?: string;
  telefono?: string;
  direccion?: string;
  ruc?: string;
  credito_limite: number;
  credito_usado: number;
  activo: boolean;
  total_compras: number;
  ultima_compra: string;
  created_at: string;
}

interface ClientsPageProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
    };
  };
  clients: Client[];
  stats: {
    total_clientes: number;
    clientes_activos: number;
    con_credito: number;
    credito_pendiente: number;
  };
  filters: {
    search?: string;
    status?: string;
    credit?: string;
  };
}

export default function Clients({ auth, clients, stats, filters }: ClientsPageProps) {
  const [searchTerm, setSearchTerm] = useState(filters.search || '');
  const [selectedStatus, setSelectedStatus] = useState(filters.status || 'all');
  const [selectedCredit, setSelectedCredit] = useState(filters.credit || 'all');

  // Filtrar clientes
  const filteredClients = clients.filter(client => {
    const matchesSearch = client.nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         client.email?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         client.ruc?.includes(searchTerm) ||
                         client.telefono?.includes(searchTerm);
                         
    const matchesStatus = selectedStatus === 'all' || 
                         (selectedStatus === 'active' && client.activo) ||
                         (selectedStatus === 'inactive' && !client.activo);
                         
    const matchesCredit = selectedCredit === 'all' || 
                         (selectedCredit === 'with_credit' && client.credito_limite > 0) ||
                         (selectedCredit === 'no_credit' && client.credito_limite === 0) ||
                         (selectedCredit === 'with_debt' && client.credito_usado > 0);
    
    return matchesSearch && matchesStatus && matchesCredit;
  });

  const getCreditStatus = (client: Client) => {
    if (client.credito_limite === 0) return { text: 'Sin crédito', color: 'gray' };
    if (client.credito_usado === 0) return { text: 'Disponible', color: 'green' };
    if (client.credito_usado >= client.credito_limite * 0.8) return { text: 'Límite próximo', color: 'red' };
    return { text: 'Con deuda', color: 'yellow' };
  };

  const handleDelete = (clientId: number) => {
    if (confirm('¿Estás seguro de eliminar este cliente?')) {
      router.delete(`/clients/${clientId}`, {
        onSuccess: () => {
          // Mostrar mensaje de éxito
        }
      });
    }
  };

  return (
    <AuthenticatedLayout
      header={
        <div className="flex justify-between items-center">
          <div>
            <h2 className="font-semibold text-xl text-gray-800 leading-tight">
              Gestión de Clientes
            </h2>
            <p className="text-gray-600 text-sm mt-1">Administra tu base de clientes y sus datos</p>
          </div>
          <div className="flex gap-3">
            <Button variant="secondary" size="sm">
              <CloudArrowDownIcon className="w-4 h-4 mr-2" />
              Exportar
            </Button>
            <Link href="/clients/create">
              <Button variant="primary" size="sm">
                <PlusIcon className="w-4 h-4 mr-2" />
                Nuevo Cliente
              </Button>
            </Link>
          </div>
        </div>
      }
    >
      <Head title="Clientes" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
          
          {/* Stats Cards */}
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div className="bg-blue-50 p-4 rounded-lg border">
              <div className="text-2xl font-bold text-blue-600">
                {stats.total_clientes.toLocaleString()}
              </div>
              <div className="text-sm text-blue-700">Total Clientes</div>
            </div>
            
            <div className="bg-green-50 p-4 rounded-lg border">
              <div className="text-2xl font-bold text-green-600">
                {stats.clientes_activos.toLocaleString()}
              </div>
              <div className="text-sm text-green-700">Activos</div>
            </div>
            
            <div className="bg-purple-50 p-4 rounded-lg border">
              <div className="text-2xl font-bold text-purple-600">
                {stats.con_credito.toLocaleString()}
              </div>
              <div className="text-sm text-purple-700">Con Crédito</div>
            </div>
            
            <div className="bg-orange-50 p-4 rounded-lg border">
              <div className="text-2xl font-bold text-orange-600">
                ${stats.credito_pendiente.toLocaleString()}
              </div>
              <div className="text-sm text-orange-700">Crédito Pendiente</div>
            </div>
          </div>

          {/* Filtros */}
          <Card>
            <CardHeader>
              <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h3 className="text-lg font-medium text-gray-900">Filtros y Búsqueda</h3>
                <div className="flex items-center gap-2">
                  <AdjustmentsHorizontalIcon className="w-5 h-5 text-gray-500" />
                  <span className="text-sm text-gray-500">
                    {filteredClients.length} de {clients.length} clientes
                  </span>
                </div>
              </div>
            </CardHeader>
            <CardBody>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {/* Search */}
                <div className="relative">
                  <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                  <input
                    type="text"
                    placeholder="Buscar clientes..."
                    className="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                  />
                </div>

                {/* Status Filter */}
                <select
                  className="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  value={selectedStatus}
                  onChange={(e) => setSelectedStatus(e.target.value)}
                >
                  <option value="all">Todos los estados</option>
                  <option value="active">Activos</option>
                  <option value="inactive">Inactivos</option>
                </select>

                {/* Credit Filter */}
                <select
                  className="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  value={selectedCredit}
                  onChange={(e) => setSelectedCredit(e.target.value)}
                >
                  <option value="all">Todos los créditos</option>
                  <option value="with_credit">Con crédito</option>
                  <option value="no_credit">Sin crédito</option>
                  <option value="with_debt">Con deuda</option>
                </select>
              </div>
            </CardBody>
          </Card>

          {/* Clients Table */}
          <Card>
            <CardHeader>
              <h3 className="text-lg font-medium text-gray-900">Lista de Clientes</h3>
            </CardHeader>
            <CardBody>
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
                        Compras
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                      </th>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {filteredClients.map((client) => {
                      const creditStatus = getCreditStatus(client);
                      return (
                        <tr key={client.id} className="hover:bg-gray-50">
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div className="flex items-center">
                              <div className="flex-shrink-0 h-10 w-10">
                                <div className="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                  <UserIcon className="h-6 w-6 text-gray-600" />
                                </div>
                              </div>
                              <div className="ml-4">
                                <div className="text-sm font-medium text-gray-900">
                                  {client.nombre}
                                </div>
                                {client.ruc && (
                                  <div className="text-sm text-gray-500">
                                    RUC: {client.ruc}
                                  </div>
                                )}
                              </div>
                            </div>
                          </td>
                          
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div className="text-sm text-gray-900">
                              {client.telefono && (
                                <div className="flex items-center mb-1">
                                  <PhoneIcon className="w-4 h-4 mr-2 text-gray-400" />
                                  {client.telefono}
                                </div>
                              )}
                              {client.email && (
                                <div className="flex items-center">
                                  <EnvelopeIcon className="w-4 h-4 mr-2 text-gray-400" />
                                  <span className="text-xs">{client.email}</span>
                                </div>
                              )}
                            </div>
                          </td>
                          
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div className="text-sm text-gray-900">
                              <div className="flex items-center mb-1">
                                <CreditCardIcon className="w-4 h-4 mr-2 text-gray-400" />
                                ${client.credito_limite.toLocaleString()}
                              </div>
                              {client.credito_usado > 0 && (
                                <div className="text-xs text-red-600">
                                  Usado: ${client.credito_usado.toLocaleString()}
                                </div>
                              )}
                              <span className={clsx(
                                "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium",
                                creditStatus.color === 'green' && "bg-green-100 text-green-800",
                                creditStatus.color === 'yellow' && "bg-yellow-100 text-yellow-800",
                                creditStatus.color === 'red' && "bg-red-100 text-red-800",
                                creditStatus.color === 'gray' && "bg-gray-100 text-gray-800"
                              )}>
                                {creditStatus.text}
                              </span>
                            </div>
                          </td>
                          
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>
                              Total: ${client.total_compras.toLocaleString()}
                            </div>
                            {client.ultima_compra && (
                              <div className="text-xs text-gray-500">
                                Última: {new Date(client.ultima_compra).toLocaleDateString()}
                              </div>
                            )}
                          </td>
                          
                          <td className="px-6 py-4 whitespace-nowrap">
                            <span className={clsx(
                              "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium",
                              client.activo
                                ? "bg-green-100 text-green-800"
                                : "bg-red-100 text-red-800"
                            )}>
                              {client.activo ? "Activo" : "Inactivo"}
                            </span>
                          </td>
                          
                          <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div className="flex items-center justify-end gap-2">
                              <Link href={`/clients/${client.id}`}>
                                <Button variant="ghost" size="sm">
                                  <EyeIcon className="w-4 h-4" />
                                </Button>
                              </Link>
                              <Link href={`/clients/${client.id}/edit`}>
                                <Button variant="ghost" size="sm">
                                  <PencilIcon className="w-4 h-4" />
                                </Button>
                              </Link>
                              <Button 
                                variant="ghost" 
                                size="sm" 
                                className="text-red-600 hover:text-red-700"
                                onClick={() => handleDelete(client.id)}
                              >
                                <TrashIcon className="w-4 h-4" />
                              </Button>
                            </div>
                          </td>
                        </tr>
                      );
                    })}
                  </tbody>
                </table>
                
                {filteredClients.length === 0 && (
                  <div className="text-center py-12">
                    <div className="text-gray-400 text-6xl mb-4">👥</div>
                    <h3 className="text-lg font-medium text-gray-900 mb-2">
                      No se encontraron clientes
                    </h3>
                    <p className="text-gray-500 mb-6">
                      {searchTerm || selectedStatus !== 'all' || selectedCredit !== 'all'
                        ? "Intenta ajustar los filtros de búsqueda"
                        : "Comienza agregando tu primer cliente"
                      }
                    </p>
                    {(!searchTerm && selectedStatus === 'all' && selectedCredit === 'all') && (
                      <Link href="/clients/create">
                        <Button variant="primary">
                          <PlusIcon className="w-4 h-4 mr-2" />
                          Crear Primer Cliente
                        </Button>
                      </Link>
                    )}
                  </div>
                )}
              </div>
            </CardBody>
          </Card>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
