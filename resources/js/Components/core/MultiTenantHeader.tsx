import React, { useState } from 'react';
import { UserCircleIcon, Cog6ToothIcon } from '@heroicons/react/24/outline';
import CompanySelector from './CompanySelector';
import BranchSelector from './BranchSelector';
import ContextIndicator from './ContextIndicator';
import MetricCard from './shared/MetricCard';
import ActionCard from './shared/ActionCard';
import { MultiTenantHeaderProps } from '@/Types/core';

const UserInfo = ({ user }: { user: any }) => (
  <MetricCard
    title="Usuario Activo"
    value={user.name}
    emoji="👤"
    subtitle={`Rol: ${user.rol_principal}`}
    color="blue"
  />
);

const QuickActions = () => (
  <ActionCard
    title="Configuración"
    description="Gestionar configuración de empresa"
    emoji="⚙️"
    href="#"
    onClick={() => {/* TODO: Implementar navegación a configuración */}}
    color="indigo"
  />
);

const WelcomeMessage = ({ user, empresa, permissions }: { user: any; empresa: any; permissions: any }) => (
  <div className="mb-6">
    <h1 className="text-2xl font-bold text-gray-900">
      {permissions.title}
    </h1>
    <p className="mt-1 text-sm text-gray-600">
      {permissions.subtitle} - <span className="font-medium text-red-600">{empresa?.nombre || 'Sin empresa'}</span>
    </p>
  </div>
);

export default function MultiTenantHeader({ 
  user, 
  empresa, 
  sucursal, 
  empresasDisponibles, 
  sucursalesDisponibles, 
  onContextChange 
}: MultiTenantHeaderProps) {
  const [isLoading, setIsLoading] = useState(false);

  // 🎯 Determinar permisos basados en el rol
  const getPermissions = (role: string) => {
    switch (role) {
      case 'owner':
        return { 
          canManageCompanies: true, 
          canManageBranches: true,
          title: '👑 Panel de Control Empresarial - OWNER',
          subtitle: 'Gestión completa de empresas y sucursales'
        };
      case 'gerente':
        return { 
          canManageCompanies: true, 
          canManageBranches: false,
          title: '🏢 Panel de Gestión de Negocios - GERENTE',
          subtitle: 'Gestión de múltiples negocios'
        };
      case 'subgerente':
        return { 
          canManageCompanies: false, 
          canManageBranches: true,
          title: '🏪 Panel de Gestión de Sucursales - SUBGERENTE',
          subtitle: 'Gestión de múltiples sucursales'
        };
      default:
        return { 
          canManageCompanies: false, 
          canManageBranches: false,
          title: '',
          subtitle: ''
        };
    }
  };

  const permissions = getPermissions(user.rol_principal);

  const handleEmpresaChange = async (empresaId: number) => {
    setIsLoading(true);
    if (onContextChange) {
      await onContextChange(empresaId, 0); // Reset sucursal when changing empresa
    }
    setIsLoading(false);
  };

  const handleSucursalChange = async (sucursalId: number) => {
    setIsLoading(true);
    if (onContextChange && empresa) {
      await onContextChange(empresa.id, sucursalId);
    }
    setIsLoading(false);
  };

  return (
    <div className="bg-gradient-to-r from-blue-50 to-indigo-50 shadow-lg border-b border-blue-200">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="py-6">
          {/* Welcome Message */}
          <WelcomeMessage user={user} empresa={empresa} permissions={permissions} />
          
          {/* Main Content usando metodología modular - Mejor organizado */}
          <div className="space-y-6">
            
            {/* Sección de Selectores */}
            {(permissions.canManageCompanies || permissions.canManageBranches) && (
              <div className="bg-white rounded-xl shadow-sm border border-blue-100 p-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-4">
                  🎯 Gestión de Contexto
                </h3>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  {/* Company Selector - Solo si puede gestionar empresas */}
                  {permissions.canManageCompanies && (
                    <div className="space-y-3">
                      <label className="block text-sm font-medium text-gray-700">
                        🏢 Empresa Activa {user.rol_principal === 'gerente' && '(Múltiples Negocios)'}
                      </label>
                      <CompanySelector
                        currentEmpresa={empresa}
                        empresasDisponibles={empresasDisponibles}
                        onEmpresaChange={handleEmpresaChange}
                        isLoading={isLoading}
                      />
                    </div>
                  )}
                  
                  {/* Branch Selector - Solo si puede gestionar sucursales */}
                  {permissions.canManageBranches && (
                    <div className="space-y-3">
                      <label className="block text-sm font-medium text-gray-700">
                        🏪 Sucursal Activa {user.rol_principal === 'subgerente' && '(Múltiples Sucursales)'}
                      </label>
                      <BranchSelector
                        currentSucursal={sucursal}
                        sucursalesDisponibles={sucursalesDisponibles}
                        onSucursalChange={handleSucursalChange}
                        isLoading={isLoading}
                      />
                    </div>
                  )}
                </div>
              </div>
            )}
            
            {/* Sección de Información y Acciones */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              {/* User Info Modular */}
              <div className="bg-white rounded-xl shadow-sm border border-blue-100 p-4">
                <UserInfo user={user} />
              </div>
              
              {/* Quick Actions Modular */}
              <div className="bg-white rounded-xl shadow-sm border border-blue-100 p-4">
                <QuickActions />
              </div>
            </div>

            {/* Context Indicator - Información adicional */}
            <div className="bg-white rounded-xl shadow-sm border border-blue-100 p-4">
              <ContextIndicator
                empresa={empresa}
                sucursal={sucursal}
                planInfo={empresa?.plan ? `Plan ${empresa.plan.nombre} activo` : undefined}
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
