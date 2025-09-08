import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';
import { Button } from '../../Components/ui/Button';
import { Card, CardHeader, CardBody } from '../../Components/ui/Card';
import {
  PlusIcon,
  ArrowLeftIcon,
  TagIcon,
  PencilIcon,
  TrashIcon,
  EyeIcon
} from '@heroicons/react/24/outline';

interface Category {
  id: number;
  nombre: string;
  descripcion?: string;
  color?: string;
  productos_count: number;
  activa: boolean;
  created_at: string;
}

interface CategoriesPageProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
      rol_principal?: string;
    };
  };
  categories: Category[];
}

export default function Categories({ auth, categories = [] }: CategoriesPageProps) {
  const [searchTerm, setSearchTerm] = useState('');

  // Datos mock mientras se implementa el backend
  const mockCategories: Category[] = [
    {
      id: 1,
      nombre: 'Bebidas',
      descripcion: 'Refrescos, jugos, aguas y bebidas en general',
      color: '#3B82F6',
      productos_count: 25,
      activa: true,
      created_at: '2025-09-01T00:00:00.000000Z'
    },
    {
      id: 2,
      nombre: 'Snacks',
      descripcion: 'Papitas, galletas, dulces y botanas',
      color: '#F59E0B',
      productos_count: 18,
      activa: true,
      created_at: '2025-09-02T00:00:00.000000Z'
    },
    {
      id: 3,
      nombre: 'Lácteos',
      descripcion: 'Leche, yogurt, quesos y productos lácteos',
      color: '#10B981',
      productos_count: 12,
      activa: true,
      created_at: '2025-09-03T00:00:00.000000Z'
    },
    {
      id: 4,
      nombre: 'Limpieza',
      descripcion: 'Productos de limpieza y cuidado del hogar',
      color: '#8B5CF6',
      productos_count: 8,
      activa: false,
      created_at: '2025-09-04T00:00:00.000000Z'
    }
  ];

  const filteredCategories = mockCategories.filter(category =>
    category.nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
    category.descripcion?.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const stats = {
    total: mockCategories.length,
    active: mockCategories.filter(c => c.activa).length,
    totalProducts: mockCategories.reduce((sum, c) => sum + c.productos_count, 0)
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
              <TagIcon className="h-5 w-5 text-red-600" />
              <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Categorías
              </h2>
            </div>
          </div>

          {/* Acciones principales */}
          <div className="flex justify-between items-center">
            <p className="text-sm text-gray-600">
              Organiza tus productos en categorías para una mejor gestión
            </p>
            <Link href="/productos/categorias/crear">
              <Button variant="primary" size="sm">
                <PlusIcon className="w-4 h-4 mr-2" />
                Nueva Categoría
              </Button>
            </Link>
          </div>
        </div>
      }
    >
      <Head title="Categorías de Productos" />

      <div className="py-6">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
          
          {/* Stats Cards */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Total Categorías</p>
                  <p className="text-2xl font-bold text-gray-900">{stats.total}</p>
                </div>
                <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                  <TagIcon className="h-6 w-6 text-blue-600" />
                </div>
              </div>
            </div>

            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Categorías Activas</p>
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
                  <p className="text-sm font-medium text-gray-600">Productos Categorizados</p>
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
                    placeholder="Buscar categorías..."
                    className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                  />
                </div>
                <span className="text-sm text-gray-500">
                  {filteredCategories.length} de {mockCategories.length} categorías
                </span>
              </div>
            </CardBody>
          </Card>

          {/* Lista de Categorías */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredCategories.map((category) => (
              <Card key={category.id} className="hover:shadow-lg transition-shadow">
                <CardBody>
                  <div className="flex items-start justify-between mb-4">
                    <div className="flex items-center space-x-3">
                      <div 
                        className="w-10 h-10 rounded-lg flex items-center justify-center"
                        style={{ backgroundColor: category.color + '20', color: category.color }}
                      >
                        <TagIcon className="h-5 w-5" />
                      </div>
                      <div>
                        <h3 className="font-medium text-gray-900">{category.nombre}</h3>
                        <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${
                          category.activa 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-red-100 text-red-800'
                        }`}>
                          {category.activa ? 'Activa' : 'Inactiva'}
                        </span>
                      </div>
                    </div>
                  </div>

                  {category.descripcion && (
                    <p className="text-sm text-gray-600 mb-4">{category.descripcion}</p>
                  )}

                  <div className="flex items-center justify-between text-sm text-gray-500 mb-4">
                    <span>{category.productos_count} productos</span>
                    <span>Creada: {new Date(category.created_at).toLocaleDateString()}</span>
                  </div>

                  <div className="flex items-center justify-end space-x-2">
                    <Link href={`/productos/categorias/${category.id}`}>
                      <Button variant="ghost" size="sm">
                        <EyeIcon className="w-4 h-4" />
                      </Button>
                    </Link>
                    <Link href={`/productos/categorias/${category.id}/editar`}>
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

          {filteredCategories.length === 0 && (
            <Card>
              <CardBody>
                <div className="text-center py-12">
                  <TagIcon className="mx-auto h-12 w-12 text-gray-400" />
                  <h3 className="mt-2 text-sm font-medium text-gray-900">No hay categorías</h3>
                  <p className="mt-1 text-sm text-gray-500">
                    {searchTerm 
                      ? "No se encontraron categorías con ese término de búsqueda"
                      : "Comienza creando tu primera categoría de productos"
                    }
                  </p>
                  {!searchTerm && (
                    <div className="mt-6">
                      <Link href="/productos/categorias/crear">
                        <Button variant="primary">
                          <PlusIcon className="w-4 h-4 mr-2" />
                          Crear Primera Categoría
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
