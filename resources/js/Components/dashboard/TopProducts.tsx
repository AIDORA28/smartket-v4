import React from 'react';
import { Card, CardHeader, CardBody } from '../ui/Card';

interface TopProductsProps {
  topProducts: Array<{
    id: number;
    nombre: string;
    total_vendido: number;
    ingresos: number;
  }>;
}

export default function TopProducts({ topProducts }: TopProductsProps) {
  if (!topProducts || topProducts.length === 0) {
    return null;
  }

  return (
    <Card className="shadow-xl border-0">
      <CardHeader className="bg-gradient-to-r from-red-50 to-amber-50 border-b border-red-100">
        <div className="flex items-center space-x-3">
          <div className="w-10 h-10 bg-gradient-to-br from-red-600 to-amber-500 rounded-xl flex items-center justify-center shadow-lg">
            <span className="text-white text-lg">🏆</span>
          </div>
          <div>
            <h3 className="text-lg font-bold text-gray-900">Productos Más Vendidos</h3>
            <p className="text-sm text-gray-600">Los favoritos de tus clientes (últimos 30 días)</p>
          </div>
        </div>
      </CardHeader>
      <CardBody>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {topProducts.slice(0, 6).map((product, index) => (
            <div key={product.id} className="bg-gradient-to-br from-gray-50 to-white p-4 rounded-xl border border-gray-100 hover:shadow-md transition-shadow">
              <div className="flex items-center justify-between mb-2">
                <span className={`inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white ${
                  index === 0 ? 'bg-amber-500' : index === 1 ? 'bg-gray-400' : index === 2 ? 'bg-amber-600' : 'bg-red-500'
                }`}>
                  {index + 1}
                </span>
                <span className="text-sm font-bold text-red-600">
                  {product.total_vendido} vendidos
                </span>
              </div>
              <h4 className="font-semibold text-gray-900 mb-1">{product.nombre}</h4>
              <p className="text-sm text-gray-600">
                Ingresos: S/ {product.ingresos.toLocaleString('es-PE', { minimumFractionDigits: 2 })}
              </p>
            </div>
          ))}
        </div>
      </CardBody>
    </Card>
  );
}
