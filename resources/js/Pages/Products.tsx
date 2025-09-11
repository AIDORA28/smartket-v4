import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout';
import ProductTabs, { TabConfig } from '../Components/products/ProductTabs';
import ProductsTable from '../Components/products/ProductsTable';
import CategoriesTable from '../Components/products/CategoriesTable';
import { Button } from '../Components/ui/Button';
import { EyeIcon, PencilIcon, TrashIcon } from '@heroicons/react/24/outline';
import { clsx } from 'clsx';
import { 
  CubeIcon, 
  TagIcon, 
  BuildingStorefrontIcon, 
  ScaleIcon
} from '@heroicons/react/24/outline';

interface ProductsPageProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
      rol_principal: string;
    };
  };
  productos: any[];
  categorias: any[];
  marcas: any[];
  unidades: any[];
  stats: {
    total_productos: number;
    total_categorias: number;
    total_marcas: number;
    total_unidades: number;
    productos_activos: number;
    stock_bajo: number;
  };
  empresa: {
    id: number;
    nombre: string;
  };
  error?: string;
}

// Componente BrandsTable inline
interface Brand {
  id: number;
  nombre: string;
  descripcion?: string;
  activo: boolean;
  productos_count: number;
  created_at: string;
}

const BrandsTable: React.FC<{
  brands: Brand[];
  onView?: (brandId: number) => void;
  onEdit?: (brandId: number) => void;
  onDelete?: (brandId: number) => void;
}> = ({ brands, onView = () => {}, onEdit = () => {}, onDelete = () => {} }) => {
  if (brands.length === 0) {
    return (
      <div className="text-center py-12">
        <div className="text-gray-400 text-6xl mb-4">🏪</div>
        <h3 className="text-lg font-medium text-gray-900 mb-2">No se encontraron marcas</h3>
        <p className="text-gray-500 mb-6">Aún no has registrado ninguna marca de productos.</p>
      </div>
    );
  }

  return (
    <table className="min-w-full divide-y divide-gray-200">
      <thead className="bg-gray-50">
        <tr>
          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th>
          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productos</th>
          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
          <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
        </tr>
      </thead>
      <tbody className="bg-white divide-y divide-gray-200">
        {brands.map((brand) => (
          <tr key={brand.id} className="hover:bg-gray-50">
            <td className="px-6 py-4 whitespace-nowrap">
              <div className="flex items-center">
                <div className="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                  <span className="text-indigo-600 text-lg">🏪</span>
                </div>
                <div className="ml-4 text-sm font-medium text-gray-900">{brand.nombre}</div>
              </div>
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{brand.descripcion || 'Sin descripción'}</td>
            <td className="px-6 py-4 whitespace-nowrap">
              <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                {brand.productos_count} productos
              </span>
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
              <span className={clsx(
                "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium",
                brand.activo ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800"
              )}>
                {brand.activo ? "Activa" : "Inactiva"}
              </span>
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div className="flex items-center justify-end gap-2">
                <Button variant="ghost" size="sm" onClick={() => onView(brand.id)}>
                  <EyeIcon className="w-4 h-4" />
                </Button>
                <Button variant="ghost" size="sm" onClick={() => onEdit(brand.id)}>
                  <PencilIcon className="w-4 h-4" />
                </Button>
                <Button variant="ghost" size="sm" className="text-red-600 hover:text-red-700" onClick={() => onDelete(brand.id)}>
                  <TrashIcon className="w-4 h-4" />
                </Button>
              </div>
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  );
};

// Componente UnitsTable inline
interface Unit {
  id: number;
  nombre: string;
  simbolo: string;
  abreviacion: string;
  tipo: string;
  activo: boolean;
  productos_count: number;
  created_at: string;
}

const UnitsTable: React.FC<{
  units: Unit[];
  onView?: (unitId: number) => void;
  onEdit?: (unitId: number) => void;
  onDelete?: (unitId: number) => void;
}> = ({ units, onView = () => {}, onEdit = () => {}, onDelete = () => {} }) => {
  if (units.length === 0) {
    return (
      <div className="text-center py-12">
        <div className="text-gray-400 text-6xl mb-4">⚖️</div>
        <h3 className="text-lg font-medium text-gray-900 mb-2">No se encontraron unidades de medida</h3>
        <p className="text-gray-500 mb-6">Aún no has registrado ninguna unidad de medida.</p>
      </div>
    );
  }

  return (
    <table className="min-w-full divide-y divide-gray-200">
      <thead className="bg-gray-50">
        <tr>
          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</th>
          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Símbolo</th>
          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productos</th>
          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
          <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
        </tr>
      </thead>
      <tbody className="bg-white divide-y divide-gray-200">
        {units.map((unit) => (
          <tr key={unit.id} className="hover:bg-gray-50">
            <td className="px-6 py-4 whitespace-nowrap">
              <div className="flex items-center">
                <div className="flex-shrink-0 h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                  <span className="text-yellow-600 text-lg">⚖️</span>
                </div>
                <div className="ml-4 text-sm font-medium text-gray-900">{unit.nombre}</div>
              </div>
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
              <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {unit.simbolo || unit.abreviacion}
              </span>
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
              <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                {unit.productos_count} productos
              </span>
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
              <span className={clsx(
                "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium",
                unit.activo ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800"
              )}>
                {unit.activo ? "Activa" : "Inactiva"}
              </span>
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div className="flex items-center justify-end gap-2">
                <Button variant="ghost" size="sm" onClick={() => onView(unit.id)}>
                  <EyeIcon className="w-4 h-4" />
                </Button>
                <Button variant="ghost" size="sm" onClick={() => onEdit(unit.id)}>
                  <PencilIcon className="w-4 h-4" />
                </Button>
                <Button variant="ghost" size="sm" className="text-red-600 hover:text-red-700" onClick={() => onDelete(unit.id)}>
                  <TrashIcon className="w-4 h-4" />
                </Button>
              </div>
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  );
};

export default function Products({ 
  auth, 
  productos = [], 
  categorias = [], 
  marcas = [], 
  unidades = [],
  stats,
  empresa,
  error 
}: ProductsPageProps) {
  const [activeTab, setActiveTab] = useState<string>('productos');

  if (error) {
    return (
      <AuthenticatedLayout>
        <Head title="Productos - Error" />
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

  // Configuración de tabs con permisos
  const userRole = auth.user.rol_principal;
  console.log('🔍 DEBUG Products - Usuario:', auth.user.name);
  console.log('🔍 DEBUG Products - Rol:', userRole);
  
  // OWNER tiene acceso completo a todo
  const isOwner = userRole === 'owner';
  const isAdmin = ['owner', 'admin'].includes(userRole);
  const canManageProducts = ['owner', 'admin', 'almacenero'].includes(userRole);

  const tabs: TabConfig[] = [
    {
      id: 'productos',
      name: 'Productos',
      icon: CubeIcon,
      count: stats.total_productos,
      locked: false, // Todos pueden ver productos
      emoji: '📦'
    },
    {
      id: 'categorias',
      name: 'Categorías',
      icon: TagIcon,
      count: stats.total_categorias,
      locked: !canManageProducts, // Solo roles que pueden gestionar productos
      emoji: '🏷️'
    },
    {
      id: 'marcas',
      name: 'Marcas',
      icon: BuildingStorefrontIcon,
      count: stats.total_marcas,
      locked: !canManageProducts, // Solo roles que pueden gestionar productos
      emoji: '🏪'
    },
    {
      id: 'unidades',
      name: 'Unidades',
      icon: ScaleIcon,
      count: stats.total_unidades,
      locked: !canManageProducts, // Solo roles que pueden gestionar productos
      emoji: '⚖️'
    }
  ];

  const handleTabChange = (tabId: string, locked: boolean) => {
    if (!locked) {
      setActiveTab(tabId);
    }
  };

  const handleProductView = (productId: number) => {
    // Navegar a la vista de detalle del producto usando Inertia
    router.visit(`/productos/${productId}`);
  };

  const handleProductEdit = (productId: number) => {
    // Por ahora mostrar mensaje de funcionalidad en desarrollo
    alert(`Editar producto ${productId} - Funcionalidad en desarrollo`);
  };

  const handleProductDelete = (productId: number) => {
    // Por ahora mostrar confirmación simple
    if (confirm(`¿Estás seguro de que quieres eliminar el producto ${productId}?`)) {
      alert('Eliminar producto - Funcionalidad en desarrollo');
    }
  };

  // Handlers para categorías
  const handleCategoryView = (categoryId: number) => {
    console.log('Ver categoría:', categoryId);
  };

  const handleCategoryEdit = (categoryId: number) => {
    console.log('Editar categoría:', categoryId);
  };

  const handleCategoryDelete = (categoryId: number) => {
    console.log('Eliminar categoría:', categoryId);
  };

  // Handlers para marcas
  const handleBrandView = (brandId: number) => {
    console.log('Ver marca:', brandId);
  };

  const handleBrandEdit = (brandId: number) => {
    console.log('Editar marca:', brandId);
  };

  const handleBrandDelete = (brandId: number) => {
    console.log('Eliminar marca:', brandId);
  };

  // Handlers para unidades
  const handleUnitView = (unitId: number) => {
    console.log('Ver unidad:', unitId);
  };

  const handleUnitEdit = (unitId: number) => {
    console.log('Editar unidad:', unitId);
  };

  const handleUnitDelete = (unitId: number) => {
    console.log('Eliminar unidad:', unitId);
  };

  const renderTabContent = () => {
    switch (activeTab) {
      case 'productos':
        return (
          <div className="bg-white rounded-lg shadow overflow-hidden">
            <div className="px-6 py-4 border-b border-gray-200">
              <h3 className="text-lg font-medium text-gray-900">
                Listado de Productos ({productos.length})
              </h3>
              <p className="mt-1 text-sm text-gray-600">
                Gestiona tu inventario de productos
              </p>
            </div>
            <div className="overflow-x-auto">
              <ProductsTable
                products={productos}
                onView={handleProductView}
                onEdit={handleProductEdit}
                onDelete={handleProductDelete}
              />
            </div>
          </div>
        );

      case 'categorias':
        return (
          <div className="bg-white rounded-lg shadow overflow-hidden">
            <div className="px-6 py-4 border-b border-gray-200">
              <h3 className="text-lg font-medium text-gray-900">
                Gestión de Categorías ({categorias.length})
              </h3>
              <p className="mt-1 text-sm text-gray-600">
                Organiza tus productos por categorías
              </p>
            </div>
            <div className="overflow-x-auto">
              <CategoriesTable 
                categories={categorias}
                onView={handleCategoryView}
                onEdit={handleCategoryEdit}
                onDelete={handleCategoryDelete}
              />
            </div>
          </div>
        );

      case 'marcas':
        return (
          <div className="bg-white rounded-lg shadow overflow-hidden">
            <div className="px-6 py-4 border-b border-gray-200">
              <h3 className="text-lg font-medium text-gray-900">
                Gestión de Marcas ({marcas.length})
              </h3>
              <p className="mt-1 text-sm text-gray-600">
                Administra las marcas de tus productos
              </p>
            </div>
            <div className="overflow-x-auto">
              <BrandsTable 
                brands={marcas}
                onView={handleBrandView}
                onEdit={handleBrandEdit}
                onDelete={handleBrandDelete}
              />
            </div>
          </div>
        );

      case 'unidades':
        return (
          <div className="bg-white rounded-lg shadow overflow-hidden">
            <div className="px-6 py-4 border-b border-gray-200">
              <h3 className="text-lg font-medium text-gray-900">
                Unidades de Medida ({unidades.length})
              </h3>
              <p className="mt-1 text-sm text-gray-600">
                Define las unidades de medida para tus productos
              </p>
            </div>
            <div className="overflow-x-auto">
              <UnitsTable 
                units={unidades}
                onView={handleUnitView}
                onEdit={handleUnitEdit}
                onDelete={handleUnitDelete}
              />
            </div>
          </div>
        );

      default:
        return null;
    }
  };

  return (
    <AuthenticatedLayout
      header={
        <div className="flex justify-between items-center">
          <h2 className="font-semibold text-xl text-gray-800 leading-tight">
            📦 Gestión de Productos - {empresa.nombre}
          </h2>
        </div>
      }
    >
      <Head title="Productos" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          
          {/* Estadísticas */}
          <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Total Productos</p>
                  <p className="text-2xl font-bold text-blue-900">{stats.total_productos}</p>
                </div>
                <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                  <span className="text-blue-600 text-xl">📦</span>
                </div>
              </div>
            </div>

            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Productos Activos</p>
                  <p className="text-2xl font-bold text-green-900">{stats.productos_activos}</p>
                </div>
                <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                  <span className="text-green-600 text-xl">✅</span>
                </div>
              </div>
            </div>

            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Stock Bajo</p>
                  <p className="text-2xl font-bold text-red-900">{stats.stock_bajo}</p>
                </div>
                <div className="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                  <span className="text-red-600 text-xl">⚠️</span>
                </div>
              </div>
            </div>

            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Categorías</p>
                  <p className="text-2xl font-bold text-purple-900">{stats.total_categorias}</p>
                </div>
                <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                  <span className="text-purple-600 text-xl">🏷️</span>
                </div>
              </div>
            </div>

            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Marcas</p>
                  <p className="text-2xl font-bold text-blue-900">{stats.total_marcas}</p>
                </div>
                <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                  <span className="text-blue-600 text-xl">🏪</span>
                </div>
              </div>
            </div>

            <div className="bg-white p-4 rounded-lg shadow border">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Unidades</p>
                  <p className="text-2xl font-bold text-yellow-900">{stats.total_unidades}</p>
                </div>
                <div className="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                  <span className="text-yellow-600 text-xl">⚖️</span>
                </div>
              </div>
            </div>
          </div>

          {/* Tabs */}
          <div className="mb-6">
            <ProductTabs
              tabs={tabs}
              activeTab={activeTab}
              onTabChange={handleTabChange}
              userRole={userRole}
            />
          </div>

          {/* Contenido de tabs */}
          {renderTabContent()}

          {/* Información de datos reales */}
          <div className="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div className="flex">
              <div>
                <h4 className="text-green-800 font-medium">✅ Datos reales cargados</h4>
                <p className="text-green-700 text-sm mt-1">
                  Mostrando datos reales de la base de datos con {categorias.length} categorías, {marcas.length} marcas y {unidades.length} unidades de medida.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
