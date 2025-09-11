import React, { useState } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { CompanySettingsIndexProps } from '@/Types/core';
import { route } from 'ziggy-js';
import { 
  Cog6ToothIcon,
  PaintBrushIcon,
  ChartBarIcon,
  GlobeAmericasIcon,
  ShieldCheckIcon,
  BellIcon,
  DocumentTextIcon,
  CloudArrowUpIcon
} from '@heroicons/react/24/outline';

const CompanySettingsIndex: React.FC = () => {
  const props = usePage().props as any;
  const { empresa, settings, can } = props;
  const [activeTab, setActiveTab] = useState('general');

  const settingsSections = [
    {
      id: 'general',
      name: 'Configuración General',
      description: 'Información básica y configuraciones regionales',
      icon: Cog6ToothIcon,
      route: 'core.company.settings.show',
      param: 'general',
      color: 'blue'
    },
    {
      id: 'notifications',
      name: 'Notificaciones',
      description: 'Configurar alertas y notificaciones del sistema',
      icon: BellIcon,
      route: 'core.company.settings.show',
      param: 'notifications',
      color: 'yellow'
    },
    {
      id: 'billing',
      name: 'Facturación',
      description: 'Configuraciones de facturación y documentos',
      icon: DocumentTextIcon,
      route: 'core.company.settings.show',
      param: 'billing',
      color: 'green'
    },
    {
      id: 'inventory',
      name: 'Inventario',
      description: 'Políticas y alertas de inventario',
      icon: ChartBarIcon,
      route: 'core.company.settings.show',
      param: 'inventory',
      color: 'purple'
    },
    {
      id: 'regional',
      name: 'Regional',
      description: 'Zona horaria, idioma y moneda',
      icon: GlobeAmericasIcon,
      route: 'core.company.settings.show',
      param: 'regional',
      color: 'indigo'
    },
    {
      id: 'security',
      name: 'Seguridad',
      description: 'Configuraciones de seguridad y acceso',
      icon: ShieldCheckIcon,
      route: 'core.company.settings.show',
      param: 'security',
      color: 'red',
      restricted: !can.view_security
    },
    {
      id: 'backup',
      name: 'Respaldos',
      description: 'Configuración de copias de seguridad',
      icon: CloudArrowUpIcon,
      route: 'core.company.settings.show',
      param: 'backup',
      color: 'gray'
    }
  ];

  const getColorClasses = (color: string) => {
    const colors = {
      blue: 'bg-blue-50 border-blue-200 text-blue-700 hover:bg-blue-100',
      yellow: 'bg-yellow-50 border-yellow-200 text-yellow-700 hover:bg-yellow-100',
      green: 'bg-green-50 border-green-200 text-green-700 hover:bg-green-100',
      purple: 'bg-purple-50 border-purple-200 text-purple-700 hover:bg-purple-100',
      indigo: 'bg-indigo-50 border-indigo-200 text-indigo-700 hover:bg-indigo-100',
      red: 'bg-red-50 border-red-200 text-red-700 hover:bg-red-100',
      gray: 'bg-gray-50 border-gray-200 text-gray-700 hover:bg-gray-100'
    };
    return colors[color as keyof typeof colors] || colors.blue;
  };

  const getIconColorClasses = (color: string) => {
    const colors = {
      blue: 'text-blue-500',
      yellow: 'text-yellow-500',
      green: 'text-green-500',
      purple: 'text-purple-500',
      indigo: 'text-indigo-500',
      red: 'text-red-500',
      gray: 'text-gray-500'
    };
    return colors[color as keyof typeof colors] || colors.blue;
  };

  return (
    <AuthenticatedLayout>
      <Head title="Configuraciones de Empresa" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          {/* Header */}
          <div className="mb-8">
            <div className="flex items-center justify-between">
              <div>
                <h1 className="text-3xl font-bold text-gray-900">
                  Configuraciones de Empresa
                </h1>
                <p className="mt-2 text-sm text-gray-600">
                  Gestiona todas las configuraciones de {empresa.nombre}
                </p>
              </div>
              <div className="flex items-center space-x-3">
                <div className="text-sm text-gray-500">
                  Plan: <span className="font-medium text-gray-900">
                    {empresa.plan?.nombre || 'Sin Plan'}
                  </span>
                </div>
              </div>
            </div>
          </div>

          {/* Company Info Card */}
          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div className="flex items-start space-x-4">
              {empresa.logo ? (
                <img 
                  src={empresa.logo} 
                  alt={empresa.nombre}
                  className="w-16 h-16 rounded-lg object-cover"
                />
              ) : (
                <div className="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                  <Cog6ToothIcon className="w-8 h-8 text-gray-400" />
                </div>
              )}
              <div className="flex-1">
                <h2 className="text-xl font-semibold text-gray-900">{empresa.nombre}</h2>
                {empresa.ruc && (
                  <p className="text-sm text-gray-600">RUC: {empresa.ruc}</p>
                )}
                {empresa.direccion && (
                  <p className="text-sm text-gray-600">{empresa.direccion}</p>
                )}
                <div className="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                  {settings?.zona_horaria_predeterminada && (
                    <span>🌍 {settings.zona_horaria_predeterminada}</span>
                  )}
                  {settings?.idioma_predeterminado && (
                    <span>🗣️ {settings.idioma_predeterminado.toUpperCase()}</span>
                  )}
                  {settings?.moneda_predeterminada && (
                    <span>💰 {settings.moneda_predeterminada}</span>
                  )}
                </div>
              </div>
            </div>
          </div>

          {/* Settings Sections Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {settingsSections.filter(section => !section.restricted).map((section) => {
              const IconComponent = section.icon;
              return (
                <Link
                  key={section.id}
                  href={route(section.route, section.param)}
                  className={`
                    relative p-6 rounded-lg border-2 transition-all duration-200 
                    hover:shadow-md hover:scale-105 cursor-pointer
                    ${getColorClasses(section.color)}
                  `}
                >
                  <div className="flex items-start space-x-4">
                    <div className={`
                      p-3 rounded-lg bg-white shadow-sm
                    `}>
                      <IconComponent className={`w-6 h-6 ${getIconColorClasses(section.color)}`} />
                    </div>
                    <div className="flex-1">
                      <h3 className="text-lg font-semibold mb-2">
                        {section.name}
                      </h3>
                      <p className="text-sm opacity-80">
                        {section.description}
                      </p>
                    </div>
                  </div>
                  
                  {/* Status indicator */}
                  <div className="absolute top-4 right-4">
                    {/* You can add configuration status indicators here */}
                    <div className="w-3 h-3 bg-green-400 rounded-full"></div>
                  </div>
                </Link>
              );
            })}
          </div>

          {/* Quick Actions */}
          <div className="mt-8 bg-gray-50 rounded-lg p-6">
            <h3 className="text-lg font-medium text-gray-900 mb-4">Acciones Rápidas</h3>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <Link
                href={route('core.company.branding.index')}
                className="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200"
              >
                <PaintBrushIcon className="w-6 h-6 text-purple-500 mr-3" />
                <div>
                  <div className="font-medium text-gray-900">Branding</div>
                  <div className="text-sm text-gray-600">Personalizar apariencia</div>
                </div>
              </Link>
              
              <Link
                href={route('core.company.analytics.index')}
                className="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200"
              >
                <ChartBarIcon className="w-6 h-6 text-blue-500 mr-3" />
                <div>
                  <div className="font-medium text-gray-900">Analytics</div>
                  <div className="text-sm text-gray-600">Ver métricas</div>
                </div>
              </Link>
              
              <Link
                href={route('core.branches.index')}
                className="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200"
              >
                <GlobeAmericasIcon className="w-6 h-6 text-green-500 mr-3" />
                <div>
                  <div className="font-medium text-gray-900">Sucursales</div>
                  <div className="text-sm text-gray-600">Gestionar sucursales</div>
                </div>
              </Link>
              
              <button
                onClick={() => window.location.reload()}
                className="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200"
              >
                <CloudArrowUpIcon className="w-6 h-6 text-gray-500 mr-3" />
                <div>
                  <div className="font-medium text-gray-900">Actualizar</div>
                  <div className="text-sm text-gray-600">Refrescar datos</div>
                </div>
              </button>
            </div>
          </div>

          {/* Help Section */}
          <div className="mt-8 bg-blue-50 rounded-lg p-6 border border-blue-200">
            <div className="flex items-start space-x-3">
              <div className="flex-shrink-0">
                <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h3 className="text-lg font-medium text-blue-900">
                  ¿Necesitas ayuda con las configuraciones?
                </h3>
                <p className="mt-2 text-sm text-blue-700">
                  Cada sección de configuración incluye ayuda contextual y ejemplos. 
                  Si tienes dudas, consulta nuestra documentación o contacta al soporte técnico.
                </p>
                <div className="mt-4">
                  <button className="text-sm font-medium text-blue-600 hover:text-blue-500">
                    Ver documentación →
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
};

export default CompanySettingsIndex;
