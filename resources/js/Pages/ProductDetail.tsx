import React from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout';
import { Button } from '../Components/ui/Button';
import { ArrowLeftIcon } from '@heroicons/react/24/outline';

interface ProductDetailProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
      rol_principal: string;
    };
  };
  producto: {
    id: number;
    nombre: string;
    descripcion?: string;
    codigo_interno?: string;
    codigo_barra?: string;
    precio_costo: number;
    precio_venta: number;
    margen_ganancia?: number;
    stock_minimo: number;
    stock_maximo: number;
    maneja_stock: boolean;
    activo: boolean;
    categoria?: {
      id: number;
      nombre: string;
    };
    marca?: {
      id: number;
      nombre: string;
    };
    unidadMedida?: {
      id: number;
      nombre: string;
    };
  };
  stock_total: number;
  empresa: {
    id: number;
    nombre: string;
  };
}

export default function ProductDetail({ auth, producto, stock_total, empresa }: ProductDetailProps) {
  const handleGoBack = () => {
    router.visit('/productos');
  };

  return (
    <AuthenticatedLayout>
      <Head title={`Producto: ${producto.nombre}`} />
      
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Header */}
        <div className="mb-8">
          <div className="flex items-center gap-4 mb-4">
            <Button variant="ghost" onClick={handleGoBack}>
              <ArrowLeftIcon className="w-5 h-5 mr-2" />
              Volver a Productos
            </Button>
          </div>
          
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-3xl font-bold text-gray-900">
                {producto.nombre}
              </h1>
              <p className="text-gray-600 mt-1">
                {producto.descripcion || 'Sin descripción'}
              </p>
            </div>
            <div className="flex items-center gap-2">
              <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${
                producto.activo 
                  ? 'bg-green-100 text-green-800' 
                  : 'bg-red-100 text-red-800'
              }`}>
                {producto.activo ? 'Activo' : 'Inactivo'}
              </span>
            </div>
          </div>
        </div>

        {/* Content Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          
          {/* Main Info */}
          <div className="lg:col-span-2 space-y-6">
            
            {/* Basic Information */}
            <div className="bg-white shadow rounded-lg p-6">
              <h2 className="text-xl font-semibold text-gray-900 mb-4">
                Información Básica
              </h2>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Código Interno
                  </label>
                  <p className="mt-1 text-sm text-gray-900">
                    {producto.codigo_interno || 'No definido'}
                  </p>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Código de Barras
                  </label>
                  <p className="mt-1 text-sm text-gray-900">
                    {producto.codigo_barra || 'No definido'}
                  </p>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Categoría
                  </label>
                  <p className="mt-1 text-sm text-gray-900">
                    {producto.categoria?.nombre || 'Sin categoría'}
                  </p>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Marca
                  </label>
                  <p className="mt-1 text-sm text-gray-900">
                    {producto.marca?.nombre || 'Sin marca'}
                  </p>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Unidad de Medida
                  </label>
                  <p className="mt-1 text-sm text-gray-900">
                    {producto.unidadMedida?.nombre || 'Sin unidad'}
                  </p>
                </div>
              </div>
            </div>

            {/* Pricing */}
            <div className="bg-white shadow rounded-lg p-6">
              <h2 className="text-xl font-semibold text-gray-900 mb-4">
                Precios
              </h2>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Precio de Costo
                  </label>
                  <p className="mt-1 text-2xl font-semibold text-gray-900">
                    ${producto.precio_costo.toLocaleString()}
                  </p>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Precio de Venta
                  </label>
                  <p className="mt-1 text-2xl font-semibold text-green-600">
                    ${producto.precio_venta.toLocaleString()}
                  </p>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">
                    Margen de Ganancia
                  </label>
                  <p className="mt-1 text-2xl font-semibold text-blue-600">
                    {producto.margen_ganancia ? `${producto.margen_ganancia}%` : 'No definido'}
                  </p>
                </div>
              </div>
            </div>

          </div>

          {/* Sidebar */}
          <div className="space-y-6">
            
            {/* Stock Information */}
            <div className="bg-white shadow rounded-lg p-6">
              <h2 className="text-xl font-semibold text-gray-900 mb-4">
                Inventario
              </h2>
              
              {producto.maneja_stock ? (
                <div className="space-y-4">
                  <div>
                    <label className="block text-sm font-medium text-gray-700">
                      Stock Actual
                    </label>
                    <p className="mt-1 text-3xl font-bold text-gray-900">
                      {stock_total} unidades
                    </p>
                    {stock_total <= producto.stock_minimo && (
                      <p className="text-sm text-red-600 font-medium mt-1">
                        ⚠️ Stock bajo - Requiere reposición
                      </p>
                    )}
                  </div>
                  
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-gray-700">
                        Stock Mínimo
                      </label>
                      <p className="mt-1 text-lg font-semibold text-orange-600">
                        {producto.stock_minimo}
                      </p>
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700">
                        Stock Máximo
                      </label>
                      <p className="mt-1 text-lg font-semibold text-blue-600">
                        {producto.stock_maximo}
                      </p>
                    </div>
                  </div>
                </div>
              ) : (
                <p className="text-gray-500">
                  Este producto no maneja inventario
                </p>
              )}
            </div>

            {/* Actions */}
            <div className="bg-white shadow rounded-lg p-6">
              <h2 className="text-xl font-semibold text-gray-900 mb-4">
                Acciones
              </h2>
              <div className="space-y-3">
                <Button 
                  variant="primary" 
                  className="w-full"
                  onClick={() => alert('Editar producto - En desarrollo')}
                >
                  Editar Producto
                </Button>
                <Button 
                  variant="secondary" 
                  className="w-full"
                  onClick={() => alert('Ajustar stock - En desarrollo')}
                  disabled={!producto.maneja_stock}
                >
                  Ajustar Stock
                </Button>
                <Button 
                  variant="ghost" 
                  className="w-full text-red-600 hover:text-red-700"
                  onClick={() => alert('Eliminar producto - En desarrollo')}
                >
                  Eliminar Producto
                </Button>
              </div>
            </div>

          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
