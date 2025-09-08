import React from 'react';
import { Link } from '@inertiajs/react';
import { Button } from '../ui/Button';
import { Card, CardHeader, CardBody } from '../ui/Card';

interface LowStockAlertsProps {
  lowStock: Array<{
    id: number;
    nombre: string;
    stock: number;
    minimo: number;
  }>;
}

export default function LowStockAlerts({ lowStock }: LowStockAlertsProps) {
  return (
    <Card className="shadow-xl border-0">
      <CardHeader className="bg-gradient-to-r from-amber-50 to-amber-100 border-b border-amber-200">
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-3">
            <div className="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
              <span className="text-white text-lg">⚠️</span>
            </div>
            <div>
              <h3 className="text-lg font-bold text-gray-900">Stock Bajo</h3>
              <p className="text-sm text-gray-600">Productos que necesitan reposición</p>
            </div>
          </div>
          <Link href="/products">
            <Button variant="ghost" size="sm" className="text-amber-600 hover:text-amber-800 hover:bg-amber-100 transition-colors">
              Ver inventario →
            </Button>
          </Link>
        </div>
      </CardHeader>
      <CardBody className="p-0">
        <div className="space-y-0">
          {lowStock?.slice(0, 5).map((product) => (
            <div key={product.id} className="flex items-center justify-between p-4 border-b border-gray-50 hover:bg-amber-50 transition-colors">
              <div className="flex items-center space-x-3">
                <div className="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                  !
                </div>
                <div>
                  <p className="font-semibold text-gray-900">{product.nombre}</p>
                  <p className="text-sm text-gray-600">
                    Mínimo: {product.minimo} unidades
                  </p>
                </div>
              </div>
              <div className="text-right">
                <p className={`font-bold text-lg ${product.stock === 0 ? 'text-red-700' : 'text-amber-600'}`}>
                  {product.stock}
                </p>
                <p className={`text-xs font-medium ${product.stock === 0 ? 'text-red-500' : 'text-amber-500'}`}>
                  {product.stock === 0 ? 'Sin stock' : 'Stock bajo'}
                </p>
              </div>
            </div>
          )) || (
            <div className="text-center py-12">
              <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span className="text-green-600 text-2xl">✅</span>
              </div>
              <p className="text-green-700 font-semibold">¡Excelente!</p>
              <p className="text-gray-600 text-sm mt-1">Stock suficiente en todos los productos</p>
            </div>
          )}
        </div>
      </CardBody>
    </Card>
  );
}
