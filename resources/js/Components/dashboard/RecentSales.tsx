import React from 'react';
import { Link } from '@inertiajs/react';
import { Button } from '../ui/Button';
import { Card, CardHeader, CardBody } from '../ui/Card';

interface RecentSalesProps {
  recentSales: Array<{
    id: number;
    cliente: string;
    total: number;
    fecha: string;
    productos: number;
  }>;
}

export default function RecentSales({ recentSales }: RecentSalesProps) {
  return (
    <Card className="shadow-xl border-0">
      <CardHeader className="bg-gradient-to-r from-red-50 to-red-100 border-b border-red-200">
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-3">
            <div className="w-10 h-10 bg-gradient-to-br from-red-600 to-red-800 rounded-xl flex items-center justify-center shadow-lg">
              <span className="text-white text-lg">💳</span>
            </div>
            <div>
              <h3 className="text-lg font-bold text-gray-900">Ventas Recientes</h3>
              <p className="text-sm text-gray-600">Últimas transacciones</p>
            </div>
          </div>
          <Link href="/sales">
            <Button variant="ghost" size="sm" className="text-red-600 hover:text-red-800 hover:bg-red-100 transition-colors">
              Ver todas →
            </Button>
          </Link>
        </div>
      </CardHeader>
      <CardBody className="p-0">
        <div className="space-y-0">
          {recentSales?.slice(0, 5).map((sale, index) => (
            <div key={sale.id} className="flex items-center justify-between p-4 border-b border-gray-50 hover:bg-red-50 transition-colors">
              <div className="flex items-center space-x-3">
                <div className="w-10 h-10 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                  {index + 1}
                </div>
                <div>
                  <p className="font-semibold text-gray-900">{sale.cliente}</p>
                  <p className="text-sm text-gray-600 flex items-center space-x-2">
                    <span>📦 {sale.productos} productos</span>
                    <span>•</span>
                    <span>🕒 {sale.fecha}</span>
                  </p>
                </div>
              </div>
              <div className="text-right">
                <p className="font-bold text-red-700 text-lg">
                  S/ {sale.total.toLocaleString('es-PE', { minimumFractionDigits: 2 })}
                </p>
              </div>
            </div>
          )) || (
            <div className="text-center py-12">
              <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span className="text-gray-400 text-2xl">💳</span>
              </div>
              <p className="text-gray-500 font-medium">No hay ventas recientes</p>
            </div>
          )}
        </div>
      </CardBody>
    </Card>
  );
}
