import React from 'react';
import { Link } from '@inertiajs/react';
import { clsx } from 'clsx';
import { Button } from '@/Components/ui/Button';
import { 
  PencilIcon, 
  TrashIcon, 
  EyeIcon,
  PhotoIcon 
} from '@heroicons/react/24/outline';

export interface Producto {
  id: number;
  codigo: string;
  nombre: string;
  descripcion?: string;
  precio_compra: number;
  precio_venta: number;
  stock: number;
  stock_minimo: number;
  categoria: {
    id: number;
    nombre: string;
  };
  imagen?: string;
  created_at: string;
  updated_at: string;
}

interface ProductTableProps {
  productos: Producto[];
  onEdit?: (producto: Producto) => void;
  onDelete?: (producto: Producto) => void;
  onView?: (producto: Producto) => void;
}

export function ProductTable({ productos, onEdit, onDelete, onView }: ProductTableProps) {
  const getStockStatus = (stock: number, stockMinimo: number) => {
    if (stock === 0) return { status: 'sin-stock', label: 'Sin Stock', color: 'text-red-600 bg-red-50' };
    if (stock <= stockMinimo) return { status: 'stock-bajo', label: 'Stock Bajo', color: 'text-yellow-600 bg-yellow-50' };
    return { status: 'stock-ok', label: 'Stock OK', color: 'text-green-600 bg-green-50' };
  };

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('es-PE', {
      style: 'currency',
      currency: 'PEN'
    }).format(amount);
  };

  if (productos.length === 0) {
    return (
      <div className="text-center py-12">
        <PhotoIcon className="mx-auto h-12 w-12 text-gray-400" />
        <h3 className="mt-2 text-sm font-medium text-gray-900">No hay productos</h3>
        <p className="mt-1 text-sm text-gray-500">
          Comienza agregando tu primer producto al inventario.
        </p>
        <div className="mt-6">
          <Link href="/productos/create">
            <Button>Agregar Producto</Button>
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
      <table className="min-w-full divide-y divide-gray-300">
        <thead className="bg-gray-50">
          <tr>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
              Producto
            </th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
              Categoría
            </th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
              Precios
            </th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
              Stock
            </th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
              Estado
            </th>
            <th className="relative px-6 py-3">
              <span className="sr-only">Acciones</span>
            </th>
          </tr>
        </thead>
        <tbody className="bg-white divide-y divide-gray-200">
          {productos.map((producto) => {
            const stockStatus = getStockStatus(producto.stock, producto.stock_minimo);
            
            return (
              <tr key={producto.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="flex items-center">
                    <div className="flex-shrink-0 h-12 w-12">
                      {producto.imagen ? (
                        <img
                          className="h-12 w-12 rounded-lg object-cover"
                          src={producto.imagen}
                          alt={producto.nombre}
                        />
                      ) : (
                        <div className="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center">
                          <PhotoIcon className="h-6 w-6 text-gray-400" />
                        </div>
                      )}
                    </div>
                    <div className="ml-4">
                      <div className="text-sm font-medium text-gray-900">
                        {producto.nombre}
                      </div>
                      <div className="text-sm text-gray-500">
                        {producto.codigo}
                      </div>
                    </div>
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {producto.categoria.nombre}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div>
                    <div className="font-medium">{formatCurrency(producto.precio_venta)}</div>
                    <div className="text-gray-500">Compra: {formatCurrency(producto.precio_compra)}</div>
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div>
                    <div className="font-medium">{producto.stock} unidades</div>
                    <div className="text-gray-500">Mínimo: {producto.stock_minimo}</div>
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={clsx(
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    stockStatus.color
                  )}>
                    {stockStatus.label}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div className="flex space-x-2">
                    {onView && (
                      <button
                        onClick={() => onView(producto)}
                        className="text-gray-400 hover:text-gray-600 transition-colors"
                        title="Ver producto"
                      >
                        <EyeIcon className="h-5 w-5" />
                      </button>
                    )}
                    {onEdit && (
                      <button
                        onClick={() => onEdit(producto)}
                        className="text-blue-400 hover:text-blue-600 transition-colors"
                        title="Editar producto"
                      >
                        <PencilIcon className="h-5 w-5" />
                      </button>
                    )}
                    {onDelete && (
                      <button
                        onClick={() => onDelete(producto)}
                        className="text-red-400 hover:text-red-600 transition-colors"
                        title="Eliminar producto"
                      >
                        <TrashIcon className="h-5 w-5" />
                      </button>
                    )}
                  </div>
                </td>
              </tr>
            );
          })}
        </tbody>
      </table>
    </div>
  );
}
