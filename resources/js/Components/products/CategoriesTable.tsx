import React from 'react';
import { clsx } from 'clsx';
import { EyeIcon, PencilIcon, TrashIcon } from '@heroicons/react/24/outline';
import { Button } from '../ui/Button';

export interface Category {
  id: number;
  nombre: string;
  descripcion: string;
  productos_count: number;
  activo: boolean;
}

interface CategoriesTableProps {
  categories: Category[];
  onView: (categoryId: number) => void;
  onEdit: (categoryId: number) => void;
  onDelete: (categoryId: number) => void;
}

export const CategoriesTable: React.FC<CategoriesTableProps> = ({
  categories,
  onView,
  onEdit,
  onDelete
}) => {
  if (categories.length === 0) {
    return (
      <div className="text-center py-12">
        <div className="text-gray-400 text-6xl mb-4">🏷️</div>
        <h3 className="text-lg font-medium text-gray-900 mb-2">
          No se encontraron categorías
        </h3>
        <p className="text-gray-500 mb-6">
          Intenta ajustar los filtros de búsqueda o crear una nueva categoría.
        </p>
      </div>
    );
  }

  return (
    <table className="min-w-full divide-y divide-gray-200">
      <thead className="bg-gray-50">
        <tr>
          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Categoría
          </th>
          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Productos
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
        {categories.map((category) => (
          <tr key={category.id} className="hover:bg-gray-50">
            <td className="px-6 py-4 whitespace-nowrap">
              <div className="flex items-center">
                <div className="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-lg flex items-center justify-center">
                  <span className="text-blue-600 text-sm">🏷️</span>
                </div>
                <div className="ml-4">
                  <div className="text-sm font-medium text-gray-900">
                    {category.nombre}
                  </div>
                  <div className="text-sm text-gray-500">
                    {category.descripcion}
                  </div>
                </div>
              </div>
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
              <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                {category.productos_count} productos
              </span>
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
              <span className={clsx(
                "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium",
                category.activo
                  ? "bg-green-100 text-green-800"
                  : "bg-red-100 text-red-800"
              )}>
                {category.activo ? "Activa" : "Inactiva"}
              </span>
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div className="flex items-center justify-end gap-2">
                <Button variant="ghost" size="sm" onClick={() => onView(category.id)}>
                  <EyeIcon className="w-4 h-4" />
                </Button>
                <Button variant="ghost" size="sm" onClick={() => onEdit(category.id)}>
                  <PencilIcon className="w-4 h-4" />
                </Button>
                <Button 
                  variant="ghost" 
                  size="sm" 
                  className="text-red-600 hover:text-red-700"
                  onClick={() => onDelete(category.id)}
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

export default CategoriesTable;
