import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';
import { Button } from '../../Components/ui/Button';
import { Card, CardHeader, CardBody } from '../../Components/ui/Card';
import {
  PlusIcon,
  ArrowLeftIcon,
  BuildingStorefrontIcon,
  PencilIcon,
  TrashIcon,
  EyeIcon,
  GlobeAltIcon
} from '@heroicons/react/24/outline';

interface Brand {
  id: number;
  nombre: string;
  descripcion?: string;
  logo?: string;
  website?: string;
  productos_count: number;
  activa: boolean;
  created_at: string;
}

interface BrandsPageProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
      rol_principal?: string;
    };
  };
  brands: Brand[];
}

export default function Brands({ auth, brands = [] }: BrandsPageProps) {
  const [searchTerm, setSearchTerm] = useState('');

  // Datos mock mientras se implementa el backend
  const mockBrands: Brand[] = [
    {
      id: 1,
      nombre: 'Coca-Cola',
      descripcion: 'Bebidas refrescantes y productos de la marca Coca-Cola',
      logo: '/img/brands/coca-cola-logo.png',
      website: 'https://coca-cola.com',
      productos_count: 15,
      activa: true,
      created_at: '2025-09-01T00:00:00.000000Z'
    },
    {
      id: 2,
      nombre: 'Nestlé',
      descripcion: 'Productos alimenticios, lácteos y dulces',
      logo: '/img/brands/nestle-logo.png',
      website: 'https://nestle.com',
      productos_count: 22,
      activa: true,
      created_at: '2025-09-02T00:00:00.000000Z'
    },
    {
      id: 3,
      nombre: 'Bimbo',
      descripcion: 'Panadería y productos horneados',
      logo: '/img/brands/bimbo-logo.png',
      website: 'https://grupobimbo.com',
      productos_count: 18,
      activa: true,
      created_at: '2025-09-03T00:00:00.000000Z'
    },
    {
      id: 4,
      nombre: 'Sabritas',
      descripcion: 'Botanas y snacks salados',
      logo: '/img/brands/sabritas-logo.png',
      website: 'https://sabritas.com.mx',
      productos_count: 12,
      activa: true,
      created_at: '2025-09-04T00:00:00.000000Z'
    },
    {
      id: 5,
      nombre: 'Genérica',
      descripcion: 'Productos sin marca específica',
      productos_count: 8,
      activa: false,
      created_at: '2025-09-05T00:00:00.000000Z'
    }
  ];

  const filteredBrands = mockBrands.filter(brand =>
    brand.nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
    brand.descripcion?.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const stats = {
    total: mockBrands.length,
    active: mockBrands.filter(b => b.activa).length,
    totalProducts: mockBrands.reduce((sum, b) => sum + b.productos_count, 0)
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
              <BuildingStorefrontIcon className="h-5 w-5 text-amber-600" />
              <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Marcas
              </h2>
            </div>
          </div>

          {/* Acciones principales */}
          <div className="flex justify-between items-center">
            <p className="text-sm text-gray-600">
              Gestiona las marcas de los productos en tu inventario
            </p>
            <Link href="/productos/marcas/crear">
              <Button variant="primary" size="sm">
                <PlusIcon className="w-4 h-4 mr-2" />
                Nueva Marca
              </Button>
            </Link>
          </div>
        </div>
      }
    >
      <Head title="Marcas de Productos" />

      <div className="py-6">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
          
          {/* Stats Cards */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Total Marcas</p>
                  <p className="text-2xl font-bold text-gray-900">{stats.total}</p>
                </div>
                <div className="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                  <BuildingStorefrontIcon className="h-6 w-6 text-amber-600" />
                </div>
              </div>
            </div>

            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Marcas Activas</p>
                  <p className="text-2xl font-bold text-green-900">{stats.active}</p>
                </div>
                <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                  <span className="text-green-600 text-xl">✅</span>
                </div>
              </div>
            </div>

            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Productos con Marca</p>
                  <p className="text-2xl font-bold text-purple-900">{stats.totalProducts}</p>
                </div>
                <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                  <span className="text-purple-600 text-xl">📦</span>
                </div>
              </div>
            </div>
          </div>

          {/* Búsqueda */}
          <Card>
            <CardBody>
              <div className="flex items-center space-x-4">
                <div className="flex-1">
                  <input
                    type="text"
                    placeholder="Buscar marcas..."
                    className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-amber-500 focus:border-amber-500"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                  />
                </div>
                <span className="text-sm text-gray-500">
                  {filteredBrands.length} de {mockBrands.length} marcas
                </span>
              </div>
            </CardBody>
          </Card>

          {/* Lista de Marcas */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredBrands.map((brand) => (
              <Card key={brand.id} className="hover:shadow-lg transition-shadow">
                <CardBody>
                  <div className="flex items-start justify-between mb-4">
                    <div className="flex items-center space-x-3">
                      <div className="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center overflow-hidden">
                        {brand.logo ? (
                          <img 
                            src={brand.logo} 
                            alt={brand.nombre}
                            className="w-full h-full object-contain"
                          />
                        ) : (
                          <BuildingStorefrontIcon className="h-6 w-6 text-amber-600" />
                        )}
                      </div>
                      <div>
                        <h3 className="font-medium text-gray-900">{brand.nombre}</h3>
                        <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${
                          brand.activa 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-red-100 text-red-800'
                        }`}>
                          {brand.activa ? 'Activa' : 'Inactiva'}
                        </span>
                      </div>
                    </div>
                  </div>

                  {brand.descripcion && (
                    <p className="text-sm text-gray-600 mb-3">{brand.descripcion}</p>
                  )}

                  {brand.website && (
                    <div className="flex items-center text-sm text-blue-600 mb-3">
                      <GlobeAltIcon className="h-4 w-4 mr-1" />
                      <a 
                        href={brand.website}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="hover:underline truncate"
                      >
                        {brand.website.replace('https://', '').replace('http://', '')}
                      </a>
                    </div>
                  )}

                  <div className="flex items-center justify-between text-sm text-gray-500 mb-4">
                    <span>{brand.productos_count} productos</span>
                    <span>Creada: {new Date(brand.created_at).toLocaleDateString()}</span>
                  </div>

                  <div className="flex items-center justify-end space-x-2">
                    <Link href={`/productos/marcas/${brand.id}`}>
                      <Button variant="ghost" size="sm">
                        <EyeIcon className="w-4 h-4" />
                      </Button>
                    </Link>
                    <Link href={`/productos/marcas/${brand.id}/editar`}>
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

          {filteredBrands.length === 0 && (
            <Card>
              <CardBody>
                <div className="text-center py-12">
                  <BuildingStorefrontIcon className="mx-auto h-12 w-12 text-gray-400" />
                  <h3 className="mt-2 text-sm font-medium text-gray-900">No hay marcas</h3>
                  <p className="mt-1 text-sm text-gray-500">
                    {searchTerm 
                      ? "No se encontraron marcas con ese término de búsqueda"
                      : "Comienza registrando las marcas de tus productos"
                    }
                  </p>
                  {!searchTerm && (
                    <div className="mt-6">
                      <Link href="/productos/marcas/crear">
                        <Button variant="primary">
                          <PlusIcon className="w-4 h-4 mr-2" />
                          Registrar Primera Marca
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
