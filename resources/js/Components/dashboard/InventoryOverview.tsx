import React from 'react';
import { Link } from '@inertiajs/react';
import { Card, CardHeader, CardBody } from '../ui/Card';
import {
  CubeIcon,
  TagIcon,
  BuildingStorefrontIcon,
  ScaleIcon,
  ArrowRightIcon,
  SparklesIcon
} from '@heroicons/react/24/outline';

interface InventoryOverviewProps {
  inventoryOverview: {
    totalProductos: number;
    totalCategorias: number;
    totalMarcas: number;
    totalUnidades: number;
    marcasPopulares?: string[];
    categoriasPopulares?: string[];
  };
}

export default function InventoryOverview({ inventoryOverview }: InventoryOverviewProps) {
  const stats = [
    {
      name: 'Productos',
      value: inventoryOverview.totalProductos,
      icon: CubeIcon,
      color: 'blue',
      emoji: '📦',
      link: '/productos'
    },
    {
      name: 'Categorías',
      value: inventoryOverview.totalCategorias,
      icon: TagIcon,
      color: 'green',
      emoji: '🏷️',
      link: '/productos?tab=categorias'
    },
    {
      name: 'Marcas',
      value: inventoryOverview.totalMarcas,
      icon: BuildingStorefrontIcon,
      color: 'purple',
      emoji: '🏪',
      link: '/productos?tab=marcas'
    },
    {
      name: 'Unidades',
      value: inventoryOverview.totalUnidades,
      icon: ScaleIcon,
      color: 'amber',
      emoji: '⚖️',
      link: '/productos?tab=unidades'
    }
  ];

  const getColorClasses = (color: string) => {
    const colors = {
      blue: 'bg-blue-50 border-blue-200 text-blue-700',
      green: 'bg-green-50 border-green-200 text-green-700',
      purple: 'bg-purple-50 border-purple-200 text-purple-700',
      amber: 'bg-amber-50 border-amber-200 text-amber-700',
    };
    return colors[color as keyof typeof colors] || colors.blue;
  };

  const getIconColorClasses = (color: string) => {
    const colors = {
      blue: 'bg-blue-100 text-blue-600',
      green: 'bg-green-100 text-green-600',
      purple: 'bg-purple-100 text-purple-600',
      amber: 'bg-amber-100 text-amber-600',
    };
    return colors[color as keyof typeof colors] || colors.blue;
  };

  return (
    <Card>
      <CardHeader>
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
              <SparklesIcon className="w-6 h-6 text-white" />
            </div>
            <div>
              <h3 className="text-lg font-semibold text-gray-900">Resumen de Inventario</h3>
              <p className="text-sm text-gray-500">Datos reales de PostgreSQL</p>
            </div>
          </div>
          <Link
            href="/productos"
            className="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 font-medium"
          >
            Ver todos
            <ArrowRightIcon className="w-4 h-4" />
          </Link>
        </div>
      </CardHeader>
      <CardBody>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {stats.map((stat) => {
            const IconComponent = stat.icon;
            return (
              <Link
                key={stat.name}
                href={stat.link}
                className={`
                  relative overflow-hidden rounded-lg border p-4 transition-all duration-200 
                  hover:scale-105 hover:shadow-md cursor-pointer
                  ${getColorClasses(stat.color)}
                `}
              >
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium opacity-75">{stat.name}</p>
                    <p className="text-2xl font-bold mt-1">{stat.value}</p>
                  </div>
                  <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${getIconColorClasses(stat.color)}`}>
                    <span className="text-lg">{stat.emoji}</span>
                  </div>
                </div>
                
                {/* Efecto visual de fondo */}
                <div className="absolute top-0 right-0 -translate-y-2 translate-x-2 opacity-10">
                  <IconComponent className="w-16 h-16" />
                </div>
              </Link>
            );
          })}
        </div>

        {/* Información adicional */}
        {(inventoryOverview.marcasPopulares || inventoryOverview.categoriasPopulares) && (
          <div className="mt-6 pt-6 border-t border-gray-200">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              
              {/* Marcas populares */}
              {inventoryOverview.marcasPopulares && inventoryOverview.marcasPopulares.length > 0 && (
                <div>
                  <h4 className="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                    🏪 Marcas principales
                  </h4>
                  <div className="space-y-2">
                    {inventoryOverview.marcasPopulares.map((marca, index) => (
                      <div key={index} className="flex items-center gap-2 text-sm">
                        <div className="w-2 h-2 bg-purple-400 rounded-full"></div>
                        <span className="text-gray-600">{marca}</span>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {/* Categorías populares */}
              {inventoryOverview.categoriasPopulares && inventoryOverview.categoriasPopulares.length > 0 && (
                <div>
                  <h4 className="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                    🏷️ Categorías principales
                  </h4>
                  <div className="space-y-2">
                    {inventoryOverview.categoriasPopulares.map((categoria, index) => (
                      <div key={index} className="flex items-center gap-2 text-sm">
                        <div className="w-2 h-2 bg-green-400 rounded-full"></div>
                        <span className="text-gray-600">{categoria}</span>
                      </div>
                    ))}
                  </div>
                </div>
              )}

            </div>
          </div>
        )}
      </CardBody>
    </Card>
  );
}
