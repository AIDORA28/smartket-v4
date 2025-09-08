import React, { useState } from 'react';
import { useForm, router } from '@inertiajs/react';
import { Button } from '../ui/Button';
import { 
  XMarkIcon, 
  PlusIcon,
  CubeIcon,
  TagIcon,
  BuildingStorefrontIcon,
  ScaleIcon,
  CurrencyDollarIcon,
  HashtagIcon,
  DocumentTextIcon
} from '@heroicons/react/24/outline';

interface Brand {
  id: number;
  nombre: string;
  descripcion: string;
}

interface Unit {
  id: number;
  nombre: string;
  abreviacion: string;
  tipo: string;
}

interface Category {
  id: number;
  nombre: string;
  descripcion: string;
}

interface ProductFormModalProps {
  isOpen: boolean;
  onClose: () => void;
  marcas: Brand[];
  unidades: Unit[];
  categorias: Category[];
  empresaId: number;
}

interface ProductFormData {
  nombre: string;
  descripcion: string;
  codigo_barras: string;
  categoria_id: number | '';
  marca_id: number | '';
  unidad_medida_id: number | '';
  precio_compra: string;
  precio_venta: string;
  stock_minimo: string;
  stock_inicial: string;
  activo: boolean;
}

export function ProductFormModal({ 
  isOpen, 
  onClose, 
  marcas, 
  unidades, 
  categorias, 
  empresaId 
}: ProductFormModalProps) {
  const { data, setData, post, processing, errors, reset } = useForm<ProductFormData>({
    nombre: '',
    descripcion: '',
    codigo_barras: '',
    categoria_id: '',
    marca_id: '',
    unidad_medida_id: '',
    precio_compra: '',
    precio_venta: '',
    stock_minimo: '0',
    stock_inicial: '0',
    activo: true,
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    // Convertir strings a números donde sea necesario
    const formData = {
      ...data,
      categoria_id: Number(data.categoria_id),
      marca_id: Number(data.marca_id),
      unidad_medida_id: Number(data.unidad_medida_id),
      precio_compra: parseFloat(data.precio_compra) || 0,
      precio_venta: parseFloat(data.precio_venta) || 0,
      stock_minimo: parseInt(data.stock_minimo) || 0,
      stock_inicial: parseInt(data.stock_inicial) || 0,
      empresa_id: empresaId,
    };

    post('/productos', {
      onSuccess: () => {
        reset();
        onClose();
      },
      onError: (errors) => {
        console.error('Errores al crear producto:', errors);
      }
    });
  };

  const handleClose = () => {
    reset();
    onClose();
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 overflow-y-auto">
      <div className="flex min-h-screen items-center justify-center p-4">
        {/* Overlay */}
        <div 
          className="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
          onClick={handleClose}
        ></div>
        
        {/* Modal */}
        <div className="relative bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          {/* Header */}
          <div className="flex items-center justify-between p-6 border-b border-gray-200">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <PlusIcon className="w-6 h-6 text-blue-600" />
              </div>
              <div>
                <h3 className="text-lg font-semibold text-gray-900">Nuevo Producto</h3>
                <p className="text-sm text-gray-500">Agregar producto con datos reales</p>
              </div>
            </div>
            <button
              onClick={handleClose}
              className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <XMarkIcon className="w-5 h-5 text-gray-500" />
            </button>
          </div>

          {/* Form */}
          <form onSubmit={handleSubmit} className="p-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              
              {/* Información básica */}
              <div className="md:col-span-2">
                <h4 className="text-sm font-medium text-gray-900 mb-4 flex items-center gap-2">
                  <CubeIcon className="w-4 h-4" />
                  Información Básica
                </h4>
              </div>

              {/* Nombre */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Nombre del Producto *
                </label>
                <input
                  type="text"
                  value={data.nombre}
                  onChange={(e) => setData('nombre', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Ej: Coca Cola 500ml"
                  required
                />
                {errors.nombre && (
                  <p className="mt-1 text-sm text-red-600">{errors.nombre}</p>
                )}
              </div>

              {/* Código de barras */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Código de Barras
                </label>
                <div className="relative">
                  <HashtagIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                  <input
                    type="text"
                    value={data.codigo_barras}
                    onChange={(e) => setData('codigo_barras', e.target.value)}
                    className="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    placeholder="7751271234567"
                  />
                </div>
                {errors.codigo_barras && (
                  <p className="mt-1 text-sm text-red-600">{errors.codigo_barras}</p>
                )}
              </div>

              {/* Descripción */}
              <div className="md:col-span-2">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Descripción
                </label>
                <div className="relative">
                  <DocumentTextIcon className="absolute left-3 top-3 w-4 h-4 text-gray-400" />
                  <textarea
                    value={data.descripcion}
                    onChange={(e) => setData('descripcion', e.target.value)}
                    rows={3}
                    className="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Descripción detallada del producto..."
                  />
                </div>
                {errors.descripcion && (
                  <p className="mt-1 text-sm text-red-600">{errors.descripcion}</p>
                )}
              </div>

              {/* Clasificación */}
              <div className="md:col-span-2">
                <h4 className="text-sm font-medium text-gray-900 mb-4 flex items-center gap-2">
                  <TagIcon className="w-4 h-4" />
                  Clasificación
                </h4>
              </div>

              {/* Categoría */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Categoría *
                </label>
                <select
                  value={data.categoria_id}
                  onChange={(e) => setData('categoria_id', e.target.value ? parseInt(e.target.value) : '')}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  required
                >
                  <option value="">Seleccionar categoría</option>
                  {categorias.map((categoria) => (
                    <option key={categoria.id} value={categoria.id}>
                      {categoria.nombre}
                    </option>
                  ))}
                </select>
                {errors.categoria_id && (
                  <p className="mt-1 text-sm text-red-600">{errors.categoria_id}</p>
                )}
              </div>

              {/* Marca */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Marca *
                </label>
                <select
                  value={data.marca_id}
                  onChange={(e) => setData('marca_id', e.target.value ? parseInt(e.target.value) : '')}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  required
                >
                  <option value="">Seleccionar marca</option>
                  {marcas.map((marca) => (
                    <option key={marca.id} value={marca.id}>
                      {marca.nombre}
                    </option>
                  ))}
                </select>
                {errors.marca_id && (
                  <p className="mt-1 text-sm text-red-600">{errors.marca_id}</p>
                )}
              </div>

              {/* Unidad de medida */}
              <div className="md:col-span-2">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Unidad de Medida *
                </label>
                <select
                  value={data.unidad_medida_id}
                  onChange={(e) => setData('unidad_medida_id', e.target.value ? parseInt(e.target.value) : '')}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  required
                >
                  <option value="">Seleccionar unidad</option>
                  {unidades.map((unidad) => (
                    <option key={unidad.id} value={unidad.id}>
                      {unidad.nombre} ({unidad.abreviacion}) - {unidad.tipo}
                    </option>
                  ))}
                </select>
                {errors.unidad_medida_id && (
                  <p className="mt-1 text-sm text-red-600">{errors.unidad_medida_id}</p>
                )}
              </div>

              {/* Precios */}
              <div className="md:col-span-2">
                <h4 className="text-sm font-medium text-gray-900 mb-4 flex items-center gap-2">
                  <CurrencyDollarIcon className="w-4 h-4" />
                  Precios y Stock
                </h4>
              </div>

              {/* Precio de compra */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Precio de Compra (S/)
                </label>
                <div className="relative">
                  <span className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">S/</span>
                  <input
                    type="number"
                    step="0.01"
                    min="0"
                    value={data.precio_compra}
                    onChange={(e) => setData('precio_compra', e.target.value)}
                    className="pl-8 pr-3 py-2 w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    placeholder="0.00"
                  />
                </div>
                {errors.precio_compra && (
                  <p className="mt-1 text-sm text-red-600">{errors.precio_compra}</p>
                )}
              </div>

              {/* Precio de venta */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Precio de Venta (S/) *
                </label>
                <div className="relative">
                  <span className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">S/</span>
                  <input
                    type="number"
                    step="0.01"
                    min="0"
                    value={data.precio_venta}
                    onChange={(e) => setData('precio_venta', e.target.value)}
                    className="pl-8 pr-3 py-2 w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    placeholder="0.00"
                    required
                  />
                </div>
                {errors.precio_venta && (
                  <p className="mt-1 text-sm text-red-600">{errors.precio_venta}</p>
                )}
              </div>

              {/* Stock mínimo */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Stock Mínimo
                </label>
                <input
                  type="number"
                  min="0"
                  value={data.stock_minimo}
                  onChange={(e) => setData('stock_minimo', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  placeholder="0"
                />
                {errors.stock_minimo && (
                  <p className="mt-1 text-sm text-red-600">{errors.stock_minimo}</p>
                )}
              </div>

              {/* Stock inicial */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Stock Inicial
                </label>
                <input
                  type="number"
                  min="0"
                  value={data.stock_inicial}
                  onChange={(e) => setData('stock_inicial', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  placeholder="0"
                />
                {errors.stock_inicial && (
                  <p className="mt-1 text-sm text-red-600">{errors.stock_inicial}</p>
                )}
              </div>

              {/* Estado */}
              <div className="md:col-span-2">
                <label className="flex items-center gap-3">
                  <input
                    type="checkbox"
                    checked={data.activo}
                    onChange={(e) => setData('activo', e.target.checked)}
                    className="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                  />
                  <span className="text-sm font-medium text-gray-700">
                    Producto activo
                  </span>
                </label>
              </div>
            </div>

            {/* Buttons */}
            <div className="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
              <Button
                type="button"
                variant="secondary"
                onClick={handleClose}
                disabled={processing}
              >
                Cancelar
              </Button>
              <Button
                type="submit"
                variant="primary"
                disabled={processing}
                loading={processing}
              >
                {processing ? 'Guardando...' : 'Crear Producto'}
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
