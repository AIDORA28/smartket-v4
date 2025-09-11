import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import MetricCard from '../../../Components/core/shared/MetricCard';
import ActionCard from '../../../Components/core/shared/ActionCard';
import {
  BuildingOfficeIcon,
  CogIcon,
  StarIcon,
  ShieldCheckIcon,
  CreditCardIcon,
  UserGroupIcon,
  DocumentTextIcon,
  PaintBrushIcon,
  MapPinIcon,
  PhoneIcon,
  EnvelopeIcon,
  GlobeAltIcon,
  BanknotesIcon,
  ChartBarIcon
} from '@heroicons/react/24/outline';

interface CompanySettingsProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
      rol_principal: string;
    };
  };
  empresa: {
    id: number;
    nombre: string;
    nombre_comercial?: string;
    ruc?: string;
    direccion?: string;
    telefono?: string;
    email?: string;
    website?: string;
    logo_url?: string;
    plan: {
      id: number;
      nombre: string;
      precio: number;
      descripcion: string;
      limite_usuarios: number;
      limite_productos: number;
      limite_sucursales: number;
      facturacion_electronica: boolean;
      reportes_avanzados: boolean;
      soporte_prioritario: boolean;
    };
    configuracion: {
      moneda: string;
      zona_horaria: string;
      formato_fecha: string;
      idioma: string;
      tema_color: string;
    };
  };
  usage: {
    usuarios_activos: number;
    productos_registrados: number;
    sucursales_activas: number;
    ventas_mes: number;
    espacio_usado: number;
  };
  planes_disponibles: Array<{
    id: number;
    nombre: string;
    precio: number;
    descripcion: string;
    caracteristicas: string[];
    recomendado?: boolean;
  }>;
}

export default function CompanySettings({ 
  auth, 
  empresa, 
  usage, 
  planes_disponibles 
}: CompanySettingsProps) {

  const [activeTab, setActiveTab] = useState('general');

  const { data, setData, post, put, processing, errors } = useForm({
    nombre: empresa.nombre || '',
    nombre_comercial: empresa.nombre_comercial || '',
    ruc: empresa.ruc || '',
    direccion: empresa.direccion || '',
    telefono: empresa.telefono || '',
    email: empresa.email || '',
    website: empresa.website || '',
    moneda: empresa.configuracion?.moneda || 'PEN',
    zona_horaria: empresa.configuracion?.zona_horaria || 'America/Lima',
    formato_fecha: empresa.configuracion?.formato_fecha || 'DD/MM/YYYY',
    idioma: empresa.configuracion?.idioma || 'es',
    tema_color: empresa.configuracion?.tema_color || 'blue'
  });

  const formatCurrency = (amount: number = 0) => {
    return new Intl.NumberFormat('es-PE', {
      style: 'currency',
      currency: 'PEN',
      minimumFractionDigits: 0
    }).format(amount);
  };

  const getUsagePercentage = (used: number, limit: number) => {
    return Math.min(Math.round((used / limit) * 100), 100);
  };

  const getUsageColor = (percentage: number) => {
    if (percentage >= 90) return 'red';
    if (percentage >= 75) return 'orange';
    return 'green';
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    put('/core/company/settings', {
      preserveScroll: true,
      onSuccess: () => {
        // Mostrar mensaje de éxito
      }
    });
  };

  const tabs = [
    { id: 'general', name: 'Información General', icon: BuildingOfficeIcon },
    { id: 'plan', name: 'Plan y Facturación', icon: CreditCardIcon },
    { id: 'configuracion', name: 'Configuración', icon: CogIcon },
    { id: 'personalizacion', name: 'Personalización', icon: PaintBrushIcon },
  ];

  return (
    <AuthenticatedLayout>
      <Head title="Configuración Empresa - SmartKet" />
      
      <div className="py-6">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          
          {/* Header */}
          <div className="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-6 text-white mb-8">
            <div className="flex items-center justify-between">
              <div>
                <h1 className="text-3xl font-bold">
                  🏢 Configuración de Empresa
                </h1>
                <p className="text-blue-100 text-lg mt-2">
                  Administra todos los aspectos de {empresa.nombre}
                </p>
              </div>
              <div className="text-right">
                <div className="flex items-center space-x-2 mb-2">
                  <StarIcon className="w-5 h-5 text-yellow-300" />
                  <span className="text-sm font-medium">
                    {empresa.plan?.nombre || 'Plan Básico'}
                  </span>
                </div>
                <Link
                  href="/core/company/upgrade"
                  className="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-sm font-medium transition-colors"
                >
                  ⬆️ Mejorar Plan
                </Link>
              </div>
            </div>
          </div>

          {/* Usage Overview */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <MetricCard
              title="👥 Usuarios"
              value={`${usage.usuarios_activos}/${empresa.plan?.limite_usuarios || '∞'}`}
              emoji="👥"
              color={getUsageColor(getUsagePercentage(usage.usuarios_activos, empresa.plan?.limite_usuarios || 100))}
              subtitle={`${getUsagePercentage(usage.usuarios_activos, empresa.plan?.limite_usuarios || 100)}% usado`}
            />
            
            <MetricCard
              title="📦 Productos"
              value={`${usage.productos_registrados}/${empresa.plan?.limite_productos || '∞'}`}
              emoji="📦"
              color={getUsageColor(getUsagePercentage(usage.productos_registrados, empresa.plan?.limite_productos || 1000))}
              subtitle={`${getUsagePercentage(usage.productos_registrados, empresa.plan?.limite_productos || 1000)}% usado`}
            />
            
            <MetricCard
              title="🏪 Sucursales"
              value={`${usage.sucursales_activas}/${empresa.plan?.limite_sucursales || '∞'}`}
              emoji="🏪"
              color={getUsageColor(getUsagePercentage(usage.sucursales_activas, empresa.plan?.limite_sucursales || 10))}
              subtitle={`${getUsagePercentage(usage.sucursales_activas, empresa.plan?.limite_sucursales || 10)}% usado`}
            />
            
            <MetricCard
              title="💰 Ventas Mes"
              value={formatCurrency(usage.ventas_mes)}
              emoji="💰"
              color="blue"
              trend={{ value: 12, isPositive: true }}
              subtitle="vs mes anterior"
            />
          </div>

          {/* Quick Actions */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">
              🚀 Acciones Rápidas
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <ActionCard
                title="Usuarios"
                description="Gestionar equipo"
                emoji="👥"
                href="/core/users"
                color="blue"
              />
              
              <ActionCard
                title="Sucursales"
                description="Administrar ubicaciones"
                emoji="🏪"
                href="/core/branches"
                color="green"
              />
              
              <ActionCard
                title="Reportes"
                description="Ver analytics"
                emoji="📊"
                href="/core/analytics"
                color="purple"
              />
              
              <ActionCard
                title="Facturación"
                description="Historial de pagos"
                emoji="🧾"
                href="/core/billing"
                color="orange"
              />
            </div>
          </div>

          {/* Tabs Navigation */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div className="border-b border-gray-200">
              <nav className="flex space-x-8 px-6" aria-label="Tabs">
                {tabs.map((tab) => {
                  const Icon = tab.icon;
                  return (
                    <button
                      key={tab.id}
                      onClick={() => setActiveTab(tab.id)}
                      className={`${
                        activeTab === tab.id
                          ? 'border-blue-500 text-blue-600'
                          : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                      } flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-colors`}
                    >
                      <Icon className="w-5 h-5 mr-2" />
                      {tab.name}
                    </button>
                  );
                })}
              </nav>
            </div>

            {/* Tab Content */}
            <div className="p-6">
              
              {/* General Information Tab */}
              {activeTab === 'general' && (
                <form onSubmit={handleSubmit} className="space-y-6">
                  <div>
                    <h3 className="text-lg font-medium text-gray-900 mb-4">
                      📋 Información Básica de la Empresa
                    </h3>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          Razón Social *
                        </label>
                        <input
                          type="text"
                          value={data.nombre}
                          onChange={(e) => setData('nombre', e.target.value)}
                          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          required
                        />
                        {errors.nombre && (
                          <p className="mt-1 text-sm text-red-600">{errors.nombre}</p>
                        )}
                      </div>
                      
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          Nombre Comercial
                        </label>
                        <input
                          type="text"
                          value={data.nombre_comercial}
                          onChange={(e) => setData('nombre_comercial', e.target.value)}
                          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                      </div>
                      
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          RUC
                        </label>
                        <input
                          type="text"
                          value={data.ruc}
                          onChange={(e) => setData('ruc', e.target.value)}
                          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="20123456789"
                        />
                      </div>
                      
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          Teléfono
                        </label>
                        <input
                          type="tel"
                          value={data.telefono}
                          onChange={(e) => setData('telefono', e.target.value)}
                          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="+51 999 999 999"
                        />
                      </div>
                      
                      <div className="md:col-span-2">
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          Dirección
                        </label>
                        <textarea
                          value={data.direccion}
                          onChange={(e) => setData('direccion', e.target.value)}
                          rows={3}
                          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Dirección completa de la empresa"
                        />
                      </div>
                      
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          Email Corporativo
                        </label>
                        <input
                          type="email"
                          value={data.email}
                          onChange={(e) => setData('email', e.target.value)}
                          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="contacto@empresa.com"
                        />
                      </div>
                      
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          Sitio Web
                        </label>
                        <input
                          type="url"
                          value={data.website}
                          onChange={(e) => setData('website', e.target.value)}
                          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="https://www.empresa.com"
                        />
                      </div>
                    </div>
                  </div>

                  <div className="flex justify-end">
                    <button
                      type="submit"
                      disabled={processing}
                      className="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-lg transition-colors"
                    >
                      {processing ? (
                        <>
                          <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                          Guardando...
                        </>
                      ) : (
                        <>
                          <DocumentTextIcon className="w-5 h-5 mr-2" />
                          Guardar Cambios
                        </>
                      )}
                    </button>
                  </div>
                </form>
              )}

              {/* Plan & Billing Tab */}
              {activeTab === 'plan' && (
                <div className="space-y-8">
                  {/* Current Plan */}
                  <div>
                    <h3 className="text-lg font-medium text-gray-900 mb-4">
                      ⭐ Plan Actual
                    </h3>
                    <div className="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 border border-blue-200">
                      <div className="flex items-center justify-between">
                        <div>
                          <h4 className="text-xl font-bold text-gray-900">{empresa.plan?.nombre}</h4>
                          <p className="text-gray-600 mt-1">{empresa.plan?.descripcion}</p>
                          <p className="text-2xl font-bold text-blue-600 mt-2">
                            {formatCurrency(empresa.plan?.precio || 0)} /mes
                          </p>
                        </div>
                        <div className="text-right">
                          <div className="flex items-center space-x-4">
                            {empresa.plan?.facturacion_electronica && (
                              <div className="flex items-center text-green-600">
                                <ShieldCheckIcon className="w-5 h-5 mr-1" />
                                <span className="text-sm">Facturación Electrónica</span>
                              </div>
                            )}
                            {empresa.plan?.reportes_avanzados && (
                              <div className="flex items-center text-blue-600">
                                <ChartBarIcon className="w-5 h-5 mr-1" />
                                <span className="text-sm">Reportes Avanzados</span>
                              </div>
                            )}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Available Plans */}
                  <div>
                    <h3 className="text-lg font-medium text-gray-900 mb-4">
                      🚀 Planes Disponibles
                    </h3>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                      {planes_disponibles.map((plan) => (
                        <div 
                          key={plan.id}
                          className={`relative bg-white rounded-xl border-2 p-6 transition-all hover:shadow-lg ${
                            plan.recomendado ? 'border-blue-500 shadow-md' : 'border-gray-200'
                          }`}
                        >
                          {plan.recomendado && (
                            <div className="absolute -top-3 left-1/2 transform -translate-x-1/2">
                              <span className="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                                ⭐ Recomendado
                              </span>
                            </div>
                          )}
                          
                          <div className="text-center">
                            <h4 className="text-xl font-bold text-gray-900">{plan.nombre}</h4>
                            <p className="text-gray-600 mt-2">{plan.descripcion}</p>
                            <p className="text-3xl font-bold text-blue-600 mt-4">
                              {formatCurrency(plan.precio)}
                              <span className="text-sm text-gray-500 font-normal">/mes</span>
                            </p>
                          </div>
                          
                          <ul className="mt-6 space-y-3">
                            {plan.caracteristicas.map((caracteristica, index) => (
                              <li key={index} className="flex items-center text-sm text-gray-600">
                                <span className="text-green-500 mr-2">✓</span>
                                {caracteristica}
                              </li>
                            ))}
                          </ul>
                          
                          <button
                            className={`w-full mt-6 px-4 py-2 rounded-lg font-medium transition-colors ${
                              plan.id === empresa.plan?.id
                                ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
                                : plan.recomendado
                                ? 'bg-blue-600 hover:bg-blue-700 text-white'
                                : 'bg-gray-200 hover:bg-gray-300 text-gray-700'
                            }`}
                            disabled={plan.id === empresa.plan?.id}
                          >
                            {plan.id === empresa.plan?.id ? 'Plan Actual' : 'Cambiar a este Plan'}
                          </button>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              )}

              {/* Configuration Tab */}
              {activeTab === 'configuracion' && (
                <form onSubmit={handleSubmit} className="space-y-6">
                  <div>
                    <h3 className="text-lg font-medium text-gray-900 mb-4">
                      ⚙️ Configuración Regional
                    </h3>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          Moneda
                        </label>
                        <select
                          value={data.moneda}
                          onChange={(e) => setData('moneda', e.target.value)}
                          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                          <option value="PEN">🇵🇪 Sol Peruano (PEN)</option>
                          <option value="USD">🇺🇸 Dólar Americano (USD)</option>
                          <option value="EUR">🇪🇺 Euro (EUR)</option>
                        </select>
                      </div>
                      
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          Zona Horaria
                        </label>
                        <select
                          value={data.zona_horaria}
                          onChange={(e) => setData('zona_horaria', e.target.value)}
                          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                          <option value="America/Lima">🇵🇪 Lima (UTC-5)</option>
                          <option value="America/New_York">🇺🇸 Nueva York (UTC-5)</option>
                          <option value="Europe/Madrid">🇪🇸 Madrid (UTC+1)</option>
                        </select>
                      </div>
                      
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          Formato de Fecha
                        </label>
                        <select
                          value={data.formato_fecha}
                          onChange={(e) => setData('formato_fecha', e.target.value)}
                          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                          <option value="DD/MM/YYYY">DD/MM/YYYY (11/09/2025)</option>
                          <option value="MM/DD/YYYY">MM/DD/YYYY (09/11/2025)</option>
                          <option value="YYYY-MM-DD">YYYY-MM-DD (2025-09-11)</option>
                        </select>
                      </div>
                      
                      <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                          Idioma
                        </label>
                        <select
                          value={data.idioma}
                          onChange={(e) => setData('idioma', e.target.value)}
                          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                          <option value="es">🇪🇸 Español</option>
                          <option value="en">🇺🇸 English</option>
                          <option value="pt">🇧🇷 Português</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div className="flex justify-end">
                    <button
                      type="submit"
                      disabled={processing}
                      className="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-lg transition-colors"
                    >
                      {processing ? (
                        <>
                          <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                          Guardando...
                        </>
                      ) : (
                        <>
                          <CogIcon className="w-5 h-5 mr-2" />
                          Guardar Configuración
                        </>
                      )}
                    </button>
                  </div>
                </form>
              )}

              {/* Personalization Tab */}
              {activeTab === 'personalizacion' && (
                <div className="space-y-6">
                  <div>
                    <h3 className="text-lg font-medium text-gray-900 mb-4">
                      🎨 Personalización Visual
                    </h3>
                    <div className="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                      <div className="flex items-start space-x-3">
                        <PaintBrushIcon className="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" />
                        <div>
                          <h4 className="font-medium text-amber-800">🚧 Próximamente</h4>
                          <p className="text-amber-700 text-sm mt-1">
                            Estamos trabajando en opciones de personalización como:
                          </p>
                          <ul className="text-amber-700 text-sm mt-2 space-y-1">
                            <li>• Logo personalizado de la empresa</li>
                            <li>• Colores y temas corporativos</li>
                            <li>• Plantillas de documentos</li>
                            <li>• Configuración de impresión</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
