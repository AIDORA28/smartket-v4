import React from 'react';
import { Link } from '@inertiajs/react';
import { Button } from '../ui/Button';
import { Card, CardHeader, CardBody } from '../ui/Card';

interface QuickActionsProps {
  features: {
    pos: boolean;
    inventario_avanzado: boolean;
    reportes: boolean;
    facturacion_electronica: boolean;
  };
}

export default function QuickActions({ features }: QuickActionsProps) {
  return (
    <Card className="border-0 shadow-xl bg-gradient-to-r from-slate-50 to-gray-50">
      <CardHeader className="bg-gradient-to-r from-red-50 to-red-100 border-b border-red-200">
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-3">
            <div className="w-12 h-12 bg-gradient-to-br from-red-600 to-red-800 rounded-xl flex items-center justify-center shadow-lg">
              <span className="text-white text-2xl">⚡</span>
            </div>
            <div>
              <h3 className="text-xl font-bold text-gray-900">Acciones Rápidas</h3>
              <p className="text-sm text-gray-600">Funciones principales de tu negocio</p>
            </div>
          </div>
        </div>
      </CardHeader>
      <CardBody className="p-6">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          {features.pos && (
            <Link href="/pos">
              <Button variant="primary" size="lg" className="w-full justify-center h-24 bg-gradient-to-br from-red-600 to-red-800 hover:from-red-700 hover:to-red-900 text-lg font-semibold shadow-xl transform hover:scale-105 transition-all duration-300">
                <div className="text-center">
                  <div className="text-3xl mb-2">🛒</div>
                  <div>POS</div>
                  <div className="text-xs opacity-80">Punto de Venta</div>
                </div>
              </Button>
            </Link>
          )}
          
          <Link href="/products">
            <Button variant="secondary" size="lg" className="w-full justify-center h-24 bg-gradient-to-br from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-lg font-semibold shadow-xl transform hover:scale-105 transition-all duration-300">
              <div className="text-center">
                <div className="text-3xl mb-2">📦</div>
                <div>Productos</div>
                <div className="text-xs opacity-80">Inventario</div>
              </div>
            </Button>
          </Link>
          
          <Link href="/clients">
            <Button variant="secondary" size="lg" className="w-full justify-center h-24 bg-gradient-to-br from-red-500 to-red-700 hover:from-red-600 hover:to-red-800 text-white text-lg font-semibold shadow-xl transform hover:scale-105 transition-all duration-300">
              <div className="text-center">
                <div className="text-3xl mb-2">👥</div>
                <div>Clientes</div>
                <div className="text-xs opacity-80">CRM</div>
              </div>
            </Button>
          </Link>
          
          {features.reportes && (
            <Link href="/reports">
              <Button variant="secondary" size="lg" className="w-full justify-center h-24 bg-gradient-to-br from-amber-600 to-red-700 hover:from-amber-700 hover:to-red-800 text-white text-lg font-semibold shadow-xl transform hover:scale-105 transition-all duration-300">
                <div className="text-center">
                  <div className="text-3xl mb-2">📊</div>
                  <div>Reportes</div>
                  <div className="text-xs opacity-80">Analytics</div>
                </div>
              </Button>
            </Link>
          )}
        </div>
      </CardBody>
    </Card>
  );
}
