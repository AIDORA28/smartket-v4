import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';
import { Button } from '../../Components/ui/Button';
import { Card, CardHeader, CardBody } from '../../Components/ui/Card';
import {
  PlusIcon,
  ArrowLeftIcon,
  ScaleIcon,
  PencilIcon,
  TrashIcon,
  EyeIcon,
  ArrowsRightLeftIcon
} from '@heroicons/react/24/outline';

interface Unit {
  id: number;
  nombre: string;
  abreviacion: string;
  tipo: 'peso' | 'volumen' | 'longitud' | 'cantidad';
  factor_base?: number; // Para conversiones
  unidad_base?: string;
  productos_count: number;
  activa: boolean;
  created_at: string;
}

interface UnitsPageProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
      rol_principal?: string;
    };
  };
  units: Unit[];
}

export default function Units({ auth, units = [] }: UnitsPageProps) {
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedType, setSelectedType] = useState<string>('all');

  // Datos mock mientras se implementa el backend
  const mockUnits: Unit[] = [
    // Peso
    {
      id: 1,
      nombre: 'Kilogramo',
      abreviacion: 'kg',
      tipo: 'peso',
      productos_count: 25,
      activa: true,
      created_at: '2025-09-01T00:00:00.000000Z'
    },
    {
      id: 2,
      nombre: 'Gramo',
      abreviacion: 'g',
      tipo: 'peso',
      factor_base: 0.001,
      unidad_base: 'kg',
      productos_count: 18,
      activa: true,
      created_at: '2025-09-02T00:00:00.000000Z'
    },
    // Volumen
    {
      id: 3,
      nombre: 'Litro',
      abreviacion: 'L',
      tipo: 'volumen',
      productos_count: 22,
      activa: true,
      created_at: '2025-09-03T00:00:00.000000Z'
    },
    {
      id: 4,
      nombre: 'Mililitro',
      abreviacion: 'ml',
      tipo: 'volumen',
      factor_base: 0.001,
      unidad_base: 'L',
      productos_count: 30,
      activa: true,
      created_at: '2025-09-04T00:00:00.000000Z'
    },
    // Cantidad
    {
      id: 5,
      nombre: 'Pieza',
      abreviacion: 'pz',
      tipo: 'cantidad',
      productos_count: 45,
      activa: true,
      created_at: '2025-09-05T00:00:00.000000Z'
    },
    {
      id: 6,
      nombre: 'Paquete',
      abreviacion: 'paq',
      tipo: 'cantidad',
      productos_count: 12,
      activa: true,
      created_at: '2025-09-06T00:00:00.000000Z'
    },
    {
      id: 7,
      nombre: 'Caja',
      abreviacion: 'cja',
      tipo: 'cantidad',
      productos_count: 8,
      activa: false,
      created_at: '2025-09-07T00:00:00.000000Z'
    }
  ];

  const filteredUnits = mockUnits.filter(unit => {
    const matchesSearch = unit.nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         unit.abreviacion.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesType = selectedType === 'all' || unit.tipo === selectedType;
    return matchesSearch && matchesType;
  });

  const stats = {
    total: mockUnits.length,
    active: mockUnits.filter(u => u.activa).length,
    totalProducts: mockUnits.reduce((sum, u) => sum + u.productos_count, 0),
    byType: {
      peso: mockUnits.filter(u => u.tipo === 'peso').length,
      volumen: mockUnits.filter(u => u.tipo === 'volumen').length,
      cantidad: mockUnits.filter(u => u.tipo === 'cantidad').length
    }
  };

  const getTypeIcon = (type: string) => {
    switch (type) {
      case 'peso': return '⚖️';
      case 'volumen': return '🫗';
      case 'longitud': return '📏';
      case 'cantidad': return '🔢';
      default: return '📦';
    }
  };

  const getTypeColor = (type: string) => {
    switch (type) {
      case 'peso': return 'bg-blue-100 text-blue-800';
      case 'volumen': return 'bg-cyan-100 text-cyan-800';
      case 'longitud': return 'bg-yellow-100 text-yellow-800';
      case 'cantidad': return 'bg-green-100 text-green-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <AuthenticatedLayout
      header={
        <div className="flex flex-col space-y-4">
          {/* Navegación de regreso */}
          <div className="flex items-center space-x-4">
            <Link href="/productos">
              <Button variant="ghost" size="sm">
                <ArrowLeftIcon className="w-4 h-4 mr-2" />
                Volver a Productos
              </Button>
            </Link>
            <div className="h-6 w-px bg-gray-300" />
            <div className="flex items-center space-x-2">
              <ScaleIcon className="h-5 w-5 text-green-600" />
              <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                Unidades de Medida
              </h2>
            </div>
          </div>

          {/* Acciones principales */}
          <div className="flex justify-between items-center">
            <p className="text-sm text-gray-600">
              Define las unidades de medida para tus productos (peso, volumen, cantidad)
            </p>
            <Link href="/productos/unidades/crear">
              <Button variant="primary" size="sm">
                <PlusIcon className="w-4 h-4 mr-2" />
                Nueva Unidad
              </Button>
            </Link>
          </div>
        </div>
      }
    >
      <Head title="Unidades de Medida" />

      <div className="py-6">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
          
          {/* Stats Cards */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Total Unidades</p>
                  <p className="text-2xl font-bold text-gray-900">{stats.total}</p>
                </div>
                <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                  <ScaleIcon className="h-6 w-6 text-green-600" />
                </div>
              </div>
            </div>

            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Peso</p>
                  <p className="text-2xl font-bold text-blue-900">{stats.byType.peso}</p>
                </div>
                <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                  <span className="text-blue-600 text-xl">⚖️</span>
                </div>
              </div>
            </div>

            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Volumen</p>
                  <p className="text-2xl font-bold text-cyan-900">{stats.byType.volumen}</p>
                </div>
                <div className="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                  <span className="text-cyan-600 text-xl">🫗</span>
                </div>
              </div>
            </div>

            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Cantidad</p>
                  <p className="text-2xl font-bold text-green-900">{stats.byType.cantidad}</p>
                </div>
                <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                  <span className="text-green-600 text-xl">🔢</span>
                </div>
              </div>
            </div>
          </div>

          {/* Filtros */}
          <Card>
            <CardBody>
              <div className="flex flex-col sm:flex-row gap-4">
                <div className="flex-1">
                  <input
                    type="text"
                    placeholder="Buscar unidades..."
                    className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                  />
                </div>
                <div className="sm:w-48">
                  <select
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                    value={selectedType}
                    onChange={(e) => setSelectedType(e.target.value)}
                  >
                    <option value="all">Todos los tipos</option>
                    <option value="peso">Peso</option>
                    <option value="volumen">Volumen</option>
                    <option value="longitud">Longitud</option>
                    <option value="cantidad">Cantidad</option>
                  </select>
                </div>
                <span className="text-sm text-gray-500 flex items-center">
                  {filteredUnits.length} de {mockUnits.length} unidades
                </span>
              </div>
            </CardBody>
          </Card>

          {/* Lista de Unidades */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredUnits.map((unit) => (
              <Card key={unit.id} className="hover:shadow-lg transition-shadow">
                <CardBody>
                  <div className="flex items-start justify-between mb-4">
                    <div className="flex items-center space-x-3">
                      <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <span className="text-xl">{getTypeIcon(unit.tipo)}</span>
                      </div>
                      <div>
                        <h3 className="font-medium text-gray-900">
                          {unit.nombre} ({unit.abreviacion})
                        </h3>
                        <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${getTypeColor(unit.tipo)}`}>
                          {unit.tipo.charAt(0).toUpperCase() + unit.tipo.slice(1)}
                        </span>
                      </div>
                    </div>
                    <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${
                      unit.activa 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-red-100 text-red-800'
                    }`}>
                      {unit.activa ? 'Activa' : 'Inactiva'}
                    </span>
                  </div>

                  {unit.factor_base && unit.unidad_base && (
                    <div className="flex items-center text-sm text-gray-600 mb-3">
                      <ArrowsRightLeftIcon className="h-4 w-4 mr-1" />
                      <span>1 {unit.abreviacion} = {unit.factor_base} {unit.unidad_base}</span>
                    </div>
                  )}

                  <div className="flex items-center justify-between text-sm text-gray-500 mb-4">
                    <span>{unit.productos_count} productos</span>
                    <span>Creada: {new Date(unit.created_at).toLocaleDateString()}</span>
                  </div>

                  <div className="flex items-center justify-end space-x-2">
                    <Link href={`/productos/unidades/${unit.id}`}>
                      <Button variant="ghost" size="sm">
                        <EyeIcon className="w-4 h-4" />
                      </Button>
                    </Link>
                    <Link href={`/productos/unidades/${unit.id}/editar`}>
                      <Button variant="ghost" size="sm">
                        <PencilIcon className="w-4 h-4" />
                      </Button>
                    </Link>
                    <Button variant="ghost" size="sm" className="text-red-600 hover:text-red-700">
                      <TrashIcon className="w-4 h-4" />
                    </Button>
                  </div>
                </CardBody>
              </Card>
            ))}
          </div>

          {filteredUnits.length === 0 && (
            <Card>
              <CardBody>
                <div className="text-center py-12">
                  <ScaleIcon className="mx-auto h-12 w-12 text-gray-400" />
                  <h3 className="mt-2 text-sm font-medium text-gray-900">No hay unidades</h3>
                  <p className="mt-1 text-sm text-gray-500">
                    {searchTerm || selectedType !== 'all'
                      ? "No se encontraron unidades con esos filtros"
                      : "Comienza definiendo las unidades de medida para tus productos"
                    }
                  </p>
                  {!searchTerm && selectedType === 'all' && (
                    <div className="mt-6">
                      <Link href="/productos/unidades/crear">
                        <Button variant="primary">
                          <PlusIcon className="w-4 h-4 mr-2" />
                          Crear Primera Unidad
                        </Button>
                      </Link>
                    </div>
                  )}
                </div>
              </CardBody>
            </Card>
          )}
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
