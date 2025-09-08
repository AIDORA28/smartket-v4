import React from 'react';
import { clsx } from 'clsx';
import { EyeIcon, PencilIcon, TrashIcon } from '@heroicons/react/24/outline';
import { Button } from '../ui/Button';

export interface Product {
  id: number;
  nombre: string;
  descripcion?: string;
  precio: number;
  stock: number; // stock_actual en el frontend se mapea como 'stock'
  minimo: number; // stock_minimo en el frontend se mapea como 'minimo'
  categoria: string; // categoria_nombre en el frontend se mapea como 'categoria'
  codigo_barras?: string; // Código de barras del producto
  imagen?: string;
  activo: boolean;
  created_at: string;
  updated_at: string;
}

interface ProductsTableProps {
  products: Product[];
  onView: (productId: number) => void;
  onEdit: (productId: number) => void;
  onDelete: (productId: number) => void;
}

export const ProductsTable: React.FC<ProductsTableProps> = ({
  products,
  onView,
  onEdit,
  onDelete
}) => {
  if (products.length === 0) {
    return (
      <div className="text-center py-12">
        <div className="text-gray-400 text-6xl mb-4">📦</div>
        <h3 className="text-lg font-medium text-gray-900 mb-2">
          No se encontraron productos
        </h3>
        <p className="text-gray-500 mb-6">
          Intenta ajustar los filtros de búsqueda o crear un nuevo producto.
        </p>
      </div>
    );
  }

  return (
    <table className="min-w-full divide-y divide-gray-200">
      <thead className="bg-gray-50">
        <tr>
          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Producto
          </th>
          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Categoría
          </th>
          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Precio
          </th>
          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Stock
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
        {products.map((product) => (
          <tr key={product.id} className="hover:bg-gray-50">
            <td className="px-6 py-4 whitespace-nowrap">
              <div className="flex items-center">
                <div className="flex-shrink-0 h-10 w-10">
                  {product.imagen ? (
                    <img 
                      className="h-10 w-10 rounded-lg object-cover" 
                      src={product.imagen} 
                      alt={product.nombre} 
                    />
                  ) : (
                    <div className="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                      <span className="text-gray-400 text-lg">📦</span>
                    </div>
                  )}
                </div>
                <div className="ml-4">
                  <div className="text-sm font-medium text-gray-900">
                    {product.nombre}
                  </div>
                  {product.descripcion && (
                    <div className="text-sm text-gray-500">
                      {product.descripcion.length > 50 
                        ? `${product.descripcion.substring(0, 50)}...`
                        : product.descripcion
                      }
                    </div>
                  )}
                </div>
              </div>
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
              <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {product.categoria}
              </span>
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              ${product.precio.toLocaleString()}
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
              <div className="text-sm text-gray-900">
                {product.stock} unidades
              </div>
              <div className="text-sm text-gray-500">
                Mín: {product.minimo}
              </div>
              {product.stock <= product.minimo && (
                <div className="text-xs text-red-600 font-medium">
                  ⚠️ Stock bajo
                </div>
              )}
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
              <span className={clsx(
                "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium",
                product.activo
                  ? "bg-green-100 text-green-800"
                  : "bg-red-100 text-red-800"
              )}>
                {product.activo ? "Activo" : "Inactivo"}
              </span>
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div className="flex items-center justify-end gap-2">
                <Button variant="ghost" size="sm" onClick={() => onView(product.id)}>
                  <EyeIcon className="w-4 h-4" />
                </Button>
                <Button variant="ghost" size="sm" onClick={() => onEdit(product.id)}>
                  <PencilIcon className="w-4 h-4" />
                </Button>
                <Button 
                  variant="ghost" 
                  size="sm" 
                  className="text-red-600 hover:text-red-700"
                  onClick={() => onDelete(product.id)}
                >
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

export default ProductsTable;
