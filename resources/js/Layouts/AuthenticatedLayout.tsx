import React, { useState } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import { 
  ChartBarIcon,
  CubeIcon,
  ShoppingCartIcon,
  UsersIcon,
  BuildingStorefrontIcon,
  Cog6ToothIcon,
  ArrowRightOnRectangleIcon,
  Bars3Icon,
  XMarkIcon,
  DocumentChartBarIcon,
  ShoppingBagIcon,
  BanknotesIcon,
  TruckIcon,
  TagIcon,
  PresentationChartLineIcon,
  FlagIcon,
  StarIcon,
  ClipboardDocumentListIcon,
  ArchiveBoxIcon,
  LockClosedIcon,
  ChevronDownIcon,
  ChevronRightIcon,
  UserCircleIcon
} from '@heroicons/react/24/outline';
import { clsx } from 'clsx';
import MultiTenantHeader from '@/Components/core/MultiTenantHeader';
import { AuthenticatedLayoutProps, User, Empresa, Sucursal, NavigationModule, NavigationSubModule } from '@/Types/core';

interface PageProps {
  auth: {
    user: User;
  };
  empresa: Empresa | null;
  sucursal: Sucursal | null;
  empresas_disponibles: Empresa[];
  sucursales_disponibles: Sucursal[];
  flash?: {
    success?: string;
    error?: string;
    warning?: string;
    message?: string;
  };
  [key: string]: any;
}

// Función para generar navegación basada en roles con sub-módulos
const getAllModules = (): NavigationModule[] => {
  return [
    { 
      name: 'Dashboard', 
      href: '/dashboard', 
      icon: ChartBarIcon,
      current: false,
      roles: ['owner', 'admin', 'vendedor', 'cajero', 'almacenero'],
      emoji: '🏠'
    },
    { 
      name: 'Core', 
      href: '/core/company/settings', 
      icon: Cog6ToothIcon,
      current: false,
      roles: ['owner', 'admin'],
      emoji: '⚙️',
      expandable: true,
      subModules: [
        {
          name: 'Configuración Empresa',
          href: '/core/company/settings',
          current: false,
          roles: ['owner', 'admin']
        },
        {
          name: 'Analytics Empresa',
          href: '/core/company/analytics',
          current: false,
          roles: ['owner', 'admin']
        },
        {
          name: 'Gestión de Sucursales',
          href: '/core/branches',
          current: false,
          roles: ['owner', 'admin']
        },
        {
          name: 'Branding',
          href: '/core/company/branding',
          current: false,
          roles: ['owner', 'admin']
        },
        {
          name: 'Gestión de Usuarios',
          href: '/core/users',
          current: false,
          roles: ['owner', 'admin']
        }
      ]
    },
    { 
      name: 'POS', 
      href: '/pos', 
      icon: ShoppingCartIcon,
      current: false,
      roles: ['owner', 'admin', 'vendedor', 'cajero'],
      badge: 'Principal',
      emoji: '💳'
    },
    { 
      name: 'Cajas', 
      href: '/cajas', 
      icon: BanknotesIcon,
      current: false,
      roles: ['owner', 'admin', 'cajero'],
      emoji: '💰'
    },
    { 
      name: 'Ventas', 
      href: '/ventas', 
      icon: ShoppingCartIcon,
      current: false,
      roles: ['owner', 'admin', 'vendedor', 'cajero'],
      emoji: '🛒'
    },
    { 
      name: 'Clientes', 
      href: '/clientes', 
      icon: UsersIcon,
      current: false,
      roles: ['owner', 'admin', 'vendedor', 'cajero'],
      emoji: '👥'
    },
    { 
      name: 'Productos', 
      href: '/productos', 
      icon: CubeIcon,
      current: false,
      roles: ['owner', 'admin', 'vendedor', 'cajero', 'almacenero'],
      emoji: '📦'
    },
    { 
      name: 'Inventario', 
      href: '/inventario', 
      icon: ClipboardDocumentListIcon,
      current: false,
      roles: ['owner', 'admin', 'almacenero'],
      emoji: '📊'
    },
    { 
      name: 'Compras', 
      href: '/compras', 
      icon: ShoppingBagIcon,
      current: false,
      roles: ['owner', 'admin', 'almacenero'],
      emoji: '🛍️'
    },
    { 
      name: 'Proveedores', 
      href: '/proveedores', 
      icon: TruckIcon,
      current: false,
      roles: ['owner', 'admin', 'almacenero'],
      emoji: '🚚'
    },
    { 
      name: 'Lotes', 
      href: '/lotes', 
      icon: ArchiveBoxIcon,
      current: false,
      roles: ['owner', 'admin', 'almacenero'],
      emoji: '📋'
    },
    { 
      name: 'Reportes', 
      href: '/reportes', 
      icon: DocumentChartBarIcon,
      current: false,
      roles: ['owner', 'admin', 'almacenero'],
      emoji: '📈'
    },
    { 
      name: 'Analytics', 
      href: '/analytics', 
      icon: PresentationChartLineIcon,
      current: false,
      roles: ['owner', 'admin'],
      badge: 'Pro',
      emoji: '🔬'
    }
  ];
};

const getNavigationByRole = (userRole: string) => {
  const allModules = getAllModules();
  
  return allModules.map(module => ({
    ...module,
    locked: !module.roles.includes(userRole),
    accessType: getAccessType(module.name, userRole),
    subModules: module.subModules?.map(subModule => ({
      ...subModule,
      locked: !subModule.roles.includes(userRole)
    }))
  }));
};

const getAccessType = (moduleName: string, userRole: string) => {
  if (userRole === 'owner') return 'full';
  
  const accessMap: Record<string, Record<string, string>> = {
    'admin': {
      'Usuarios': 'limited',
      'POS': 'full',
      'Ventas': 'full', 
      'Clientes': 'full',
      'Productos': 'full',
      'Inventario': 'full',
      'Reportes': 'full'
    },
    'vendedor': {
      'Productos': 'readonly',
      'POS': 'full',
      'Ventas': 'full',
      'Clientes': 'full'
    },
    'cajero': {
      'Productos': 'readonly',
      'POS': 'full', 
      'Ventas': 'full',
      'Clientes': 'full'
    },
    'almacenero': {
      'Productos': 'full',
      'Inventario': 'full',
      'Compras': 'full',
      'Proveedores': 'full',
      'Lotes': 'full',
      'Reportes': 'limited'
    }
  };
  
  return accessMap[userRole]?.[moduleName] || 'full';
};

export default function AuthenticatedLayout({ 
  children, 
  header, 
  title 
}: AuthenticatedLayoutProps) {
  const { auth, empresa, sucursal, empresas_disponibles, sucursales_disponibles, flash } = usePage<PageProps>().props;
  const currentUrl = usePage().url; // Obtener URL actual de Inertia
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [showUserMenu, setShowUserMenu] = useState(false);
  const [expandedModules, setExpandedModules] = useState<Record<string, boolean>>({});
  const [showTenantHeader, setShowTenantHeader] = useState(false);

  // 🎯 Función para determinar qué roles pueden acceder al selector de contexto
  const canAccessTenantSelector = (role: string): boolean => {
    const allowedRoles = ['owner', 'gerente', 'subgerente'];
    return allowedRoles.includes(role);
  };

  // 🎯 Determinar qué puede gestionar cada rol
  const getTenantPermissions = (role: string) => {
    switch (role) {
      case 'owner':
        return { canManageCompanies: true, canManageBranches: true };
      case 'gerente':
        return { canManageCompanies: true, canManageBranches: false };
      case 'subgerente':
        return { canManageCompanies: false, canManageBranches: true };
      default:
        return { canManageCompanies: false, canManageBranches: false };
    }
  };

  // DEBUG: Log del rol del usuario
  console.log('🔍 DEBUG - Usuario:', auth.user.name);
  console.log('🔍 DEBUG - Rol Principal:', auth.user.rol_principal);
  console.log('🔍 DEBUG - Usuario completo:', auth.user);

  // Generar navegación dinámica basada en el rol del usuario
  const userRole = auth.user.rol_principal || 'staff';
  const navigation = getNavigationByRole(userRole);

  // Actualizar el estado 'current' basado en la URL actual
  const navigationWithCurrentState = navigation.map(item => ({
    ...item,
    current: currentUrl.startsWith(item.href) || (item.href === '/dashboard' && currentUrl === '/')
  }));

  const handleLogout = () => {
    router.post('/logout');
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Mobile sidebar overlay */}
      <div className={clsx(
        'relative z-50 lg:hidden',
        sidebarOpen ? 'block' : 'hidden'
      )}>
        <div className="fixed inset-0 bg-gray-600 bg-opacity-75" />
        
        <div className="fixed inset-0 flex">
          <div className="relative mr-16 flex w-full max-w-xs flex-1">
            <div className="absolute left-full top-0 flex w-16 justify-center pt-5">
              <button
                type="button"
                className="-m-2.5 p-2.5"
                onClick={() => setSidebarOpen(false)}
              >
                <XMarkIcon className="h-6 w-6 text-white" />
              </button>
            </div>

            <div className="flex grow flex-col gap-y-5 overflow-y-auto bg-gradient-to-b from-red-600 to-red-800 px-6 pb-4 ring-1 ring-white/10">
              <div className="flex h-16 shrink-0 items-center">
                <div className="flex items-center space-x-3">
                  <img 
                    src="/img/SmartKet.svg" 
                    alt="SmartKet" 
                    className="h-8 w-auto filter brightness-0 invert"
                  />
                  <span className="text-lg font-bold text-white">
                    <span className="text-amber-300 uppercase tracking-wide">SMART</span>
                    <span className="lowercase">ket</span>
                  </span>
                </div>
              </div>
              
              <nav className="flex flex-1 flex-col">
                <ul role="list" className="flex flex-1 flex-col gap-y-7">
                  <li>
                    <ul role="list" className="-mx-2 space-y-1">
                      {navigationWithCurrentState.map((item: any) => (
                        <li key={item.name}>
                          {/* Módulo principal móvil */}
                          <div>
                            <Link
                              href={item.locked ? '#' : (item.expandable ? '#' : item.href)}
                              className={clsx(
                                item.current
                                  ? 'bg-red-700 text-white'
                                  : item.locked 
                                  ? 'text-red-400 bg-red-900/30 cursor-not-allowed'
                                  : 'text-red-200 hover:text-white hover:bg-red-700',
                                'group flex items-center justify-between rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200 relative'
                              )}
                              onClick={(e) => {
                                if (item.locked) {
                                  e.preventDefault();
                                  return;
                                }
                                if (item.expandable) {
                                  e.preventDefault();
                                  setExpandedModules(prev => ({
                                    ...prev,
                                    [item.name]: !prev[item.name]
                                  }));
                                }
                              }}
                            >
                              <div className="flex items-center gap-x-3">
                                <div className="relative">
                                  <item.icon className={clsx(
                                    "h-6 w-6 shrink-0",
                                    item.locked && "opacity-50"
                                  )} />
                                  {item.locked && (
                                    <LockClosedIcon className="h-3 w-3 absolute -top-1 -right-1 text-red-300 bg-red-800 rounded-full p-0.5" />
                                  )}
                                </div>
                                <span className={item.locked ? "opacity-75" : ""}>
                                  {item.emoji && <span className="mr-2">{item.emoji}</span>}
                                  {item.name}
                                </span>
                              </div>
                              <div className="flex items-center gap-x-2">
                                {item.expandable && !item.locked && (
                                  expandedModules[item.name] 
                                    ? <ChevronDownIcon className="h-4 w-4" />
                                    : <ChevronRightIcon className="h-4 w-4" />
                                )}
                                {item.badge && (
                                  <span className={clsx(
                                    "inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium",
                                    item.locked 
                                      ? "bg-gray-400 text-gray-700"
                                      : "bg-amber-400 text-red-800"
                                  )}>
                                    {item.badge}
                                  </span>
                                )}
                                {item.locked && (
                                  <span className="text-xs text-red-400 font-medium">
                                    Restringido
                                  </span>
                                )}
                              </div>
                            </Link>
                            
                            {/* Sub-módulos móvil */}
                            {item.expandable && expandedModules[item.name] && item.subModules && (
                              <ul className="mt-2 ml-6 space-y-1">
                                {item.subModules.map((subItem: any) => (
                                  <li key={subItem.name}>
                                    <Link
                                      href={subItem.locked ? '#' : subItem.href}
                                      className={clsx(
                                        subItem.current
                                          ? 'bg-red-700 text-white'
                                          : subItem.locked 
                                          ? 'text-red-400 bg-red-900/20 cursor-not-allowed'
                                          : 'text-red-200 hover:text-white hover:bg-red-700',
                                        'group flex items-center justify-between rounded-md p-2 text-sm leading-6 transition-colors duration-200 relative'
                                      )}
                                      onClick={(e) => {
                                        if (subItem.locked) {
                                          e.preventDefault();
                                        }
                                      }}
                                    >
                                      <div className="flex items-center gap-x-3">
                                        <div className="w-2 h-2 rounded-full bg-red-300 opacity-60" />
                                        <span className={subItem.locked ? "opacity-75" : ""}>
                                          {subItem.name}
                                        </span>
                                      </div>
                                      {subItem.locked && (
                                        <div className="flex items-center gap-x-2">
                                          <LockClosedIcon className="h-3 w-3 text-red-300" />
                                          <span className="text-xs text-red-400 font-medium">
                                            Restringido
                                          </span>
                                        </div>
                                      )}
                                    </Link>
                                  </li>
                                ))}
                              </ul>
                            )}
                          </div>
                        </li>
                      ))}
                    </ul>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>

      {/* Static sidebar for desktop */}
      <div className="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
        <div className="flex grow flex-col gap-y-5 overflow-y-auto bg-gradient-to-b from-red-600 to-red-800 px-6 pb-4">
          <div className="flex h-16 shrink-0 items-center justify-between">
            <div className="flex items-center space-x-3">
              <img 
                src="/img/SmartKet.svg" 
                alt="SmartKet" 
                className="h-8 w-auto filter brightness-0 invert"
              />
              <span className="text-lg font-bold text-white">
                <span className="text-amber-300 uppercase tracking-wide">SMART</span>
                <span className="lowercase">ket</span>
              </span>
            </div>
            {empresa && (
              <span className="text-xs text-red-200 bg-red-700 px-2 py-1 rounded">
                {empresa.nombre}
              </span>
            )}
          </div>
          
          <nav className="flex flex-1 flex-col">
            <ul role="list" className="flex flex-1 flex-col gap-y-7">
              <li>
                <ul role="list" className="-mx-2 space-y-1">
                  {navigationWithCurrentState.map((item: any) => (
                    <li key={item.name}>
                      {/* Módulo principal */}
                      <div>
                        <Link
                          href={item.locked ? '#' : (item.expandable ? '#' : item.href)}
                          className={clsx(
                            item.current
                              ? 'bg-red-700 text-white'
                              : item.locked 
                              ? 'text-red-400 bg-red-900/30 cursor-not-allowed'
                              : 'text-red-200 hover:text-white hover:bg-red-700',
                            'group flex items-center justify-between rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200 relative'
                          )}
                          onClick={(e) => {
                            if (item.locked) {
                              e.preventDefault();
                              return;
                            }
                            if (item.expandable) {
                              e.preventDefault();
                              setExpandedModules(prev => ({
                                ...prev,
                                [item.name]: !prev[item.name]
                              }));
                            }
                          }}
                        >
                          <div className="flex items-center gap-x-3">
                            <div className="relative">
                              <item.icon className={clsx(
                                "h-6 w-6 shrink-0",
                                item.locked && "opacity-50"
                              )} />
                              {item.locked && (
                                <LockClosedIcon className="h-3 w-3 absolute -top-1 -right-1 text-red-300 bg-red-800 rounded-full p-0.5" />
                              )}
                            </div>
                            <span className={item.locked ? "opacity-75" : ""}>
                              {item.emoji && <span className="mr-2">{item.emoji}</span>}
                              {item.name}
                            </span>
                          </div>
                          <div className="flex items-center gap-x-2">
                            {item.expandable && !item.locked && (
                              expandedModules[item.name] 
                                ? <ChevronDownIcon className="h-4 w-4" />
                                : <ChevronRightIcon className="h-4 w-4" />
                            )}
                            {item.badge && (
                              <span className={clsx(
                                "inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium",
                                item.locked 
                                  ? "bg-gray-400 text-gray-700"
                                  : "bg-amber-400 text-red-800"
                              )}>
                                {item.badge}
                              </span>
                            )}
                            {item.locked && (
                              <span className="text-xs text-red-400 font-medium">
                                Restringido
                              </span>
                            )}
                          </div>
                        </Link>
                        
                        {/* Sub-módulos */}
                        {item.expandable && expandedModules[item.name] && item.subModules && (
                          <ul className="mt-2 ml-6 space-y-1">
                            {item.subModules.map((subItem: any) => (
                              <li key={subItem.name}>
                                <Link
                                  href={subItem.locked ? '#' : subItem.href}
                                  className={clsx(
                                    subItem.current
                                      ? 'bg-red-700 text-white'
                                      : subItem.locked 
                                      ? 'text-red-400 bg-red-900/20 cursor-not-allowed'
                                      : 'text-red-200 hover:text-white hover:bg-red-700',
                                    'group flex items-center justify-between rounded-md p-2 text-sm leading-6 transition-colors duration-200 relative'
                                  )}
                                  onClick={(e) => {
                                    if (subItem.locked) {
                                      e.preventDefault();
                                    }
                                  }}
                                >
                                  <div className="flex items-center gap-x-3">
                                    <div className="w-2 h-2 rounded-full bg-red-300 opacity-60" />
                                    <span className={subItem.locked ? "opacity-75" : ""}>
                                      {subItem.name}
                                    </span>
                                  </div>
                                  {subItem.locked && (
                                    <div className="flex items-center gap-x-2">
                                      <LockClosedIcon className="h-3 w-3 text-red-300" />
                                      <span className="text-xs text-red-400 font-medium">
                                        Restringido
                                      </span>
                                    </div>
                                  )}
                                </Link>
                              </li>
                            ))}
                          </ul>
                        )}
                      </div>
                    </li>
                  ))}
                </ul>
              </li>
              
              <li className="mt-auto">
                <Link
                  href="/logout"
                  method="post"
                  className="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-red-200 hover:bg-red-700 hover:text-white transition-colors duration-200"
                >
                  <ArrowRightOnRectangleIcon className="h-6 w-6 shrink-0" />
                  Cerrar Sesión
                </Link>
              </li>
            </ul>
          </nav>
        </div>
      </div>

      <div className="lg:pl-72">
        {/* Navbar Superior Mejorado */}
        <div className="sticky top-0 z-40 flex h-20 shrink-0 items-center gap-x-4 border-b border-red-200 bg-gradient-to-r from-white to-red-50 px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
          <button
            type="button"
            className="-m-2.5 p-2.5 text-red-700 lg:hidden hover:bg-red-100 rounded-md transition-colors"
            onClick={() => setSidebarOpen(true)}
          >
            <Bars3Icon className="h-6 w-6" />
          </button>

          <div className="h-6 w-px bg-red-200 lg:hidden" />

          {/* Información de la empresa y plan */}
          <div className="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
            <div className="flex flex-1 items-center">
              <div className="flex items-center space-x-4">
                {/* Logo/Icono de la empresa */}
                <div className="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-red-600 to-red-800 rounded-xl shadow-md">
                  <span className="text-white font-bold text-lg">
                    {empresa?.nombre?.charAt(0)?.toUpperCase() || 'E'}
                  </span>
                </div>
                
                {/* Información empresarial */}
                <div className="hidden sm:flex sm:flex-col">
                  <div className="flex items-center space-x-2">
                    <h1 className="text-lg font-bold text-gray-900">
                      {empresa?.nombre || 'Empresa'}
                    </h1>
                    {/* Solo el owner ve el plan */}
                    {userRole === 'owner' && empresa && (
                      <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Plan PROFESIONAL
                      </span>
                    )}
                  </div>
                  <div className="flex items-center space-x-1 text-sm text-gray-600">
                    <span className="flex items-center">
                      <BuildingStorefrontIcon className="h-4 w-4 mr-1" />
                      {sucursal?.nombre || 'Sucursal Principal'}
                    </span>
                    {/* Solo el owner puede cambiar de sucursal */}
                    {userRole === 'owner' && sucursales_disponibles?.length > 1 && (
                      <ChevronDownIcon className="h-4 w-4 text-gray-400 ml-1 cursor-pointer" />
                    )}
                  </div>
                </div>
                
                {/* Información móvil simplificada */}
                <div className="flex sm:hidden flex-col">
                  <h1 className="text-base font-bold text-gray-900 truncate max-w-32">
                    {empresa?.nombre || 'Empresa'}
                  </h1>
                  <span className="text-xs text-gray-600 truncate max-w-32">
                    {sucursal?.nombre || 'Sucursal'}
                  </span>
                </div>
              </div>
            </div>
            
            {/* Acciones rápidas y usuario */}
            <div className="flex items-center gap-x-4 lg:gap-x-6">
              {/* Botón de Contexto Multi-Tenant (solo para roles permitidos) */}
              {canAccessTenantSelector(userRole) && (
                <button
                  onClick={() => setShowTenantHeader(!showTenantHeader)}
                  className={`relative flex items-center justify-center w-10 h-10 rounded-lg transition-all duration-200 hover:scale-105 ${
                    showTenantHeader 
                      ? 'bg-blue-100 text-blue-600 shadow-md' 
                      : 'bg-gray-100 hover:bg-gray-200 text-gray-600'
                  }`}
                  title={`${showTenantHeader ? 'Ocultar' : 'Mostrar'} gestión de contexto`}
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  {/* Indicador de rol */}
                  {userRole === 'owner' && (
                    <span className="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 bg-green-500 text-white text-xs font-bold rounded-full">
                      👑
                    </span>
                  )}
                  {userRole === 'gerente' && (
                    <span className="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 bg-blue-500 text-white text-xs font-bold rounded-full">
                      🏢
                    </span>
                  )}
                  {userRole === 'subgerente' && (
                    <span className="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 bg-purple-500 text-white text-xs font-bold rounded-full">
                      🏪
                    </span>
                  )}
                </button>
              )}
              
              {/* Notificaciones */}
              <div className="relative">
                <button
                  type="button"
                  className="relative flex items-center justify-center w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                  title="Notificaciones"
                >
                  <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                  </svg>
                  {/* Badge de notificaciones */}
                  <span className="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full">
                    3
                  </span>
                </button>
              </div>

              {/* Separador */}
              <div className="hidden lg:block lg:h-8 lg:w-px lg:bg-gray-200" />
              
              {/* Información del usuario */}
              <div className="flex items-center space-x-3">
                <div className="hidden lg:block text-right">
                  <p className="text-sm font-semibold text-gray-900">{auth.user.name}</p>
                  <p className="text-xs text-gray-500 capitalize">{auth.user.rol_principal}</p>
                </div>
                
                {/* Avatar del usuario */}
                <div className="relative">
                  <button
                    onClick={() => setShowUserMenu(!showUserMenu)}
                    className="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-red-600 to-red-700 rounded-full shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105"
                  >
                    <span className="text-white font-bold text-sm">
                      {auth.user.name.charAt(0).toUpperCase()}
                    </span>
                  </button>
                  
                  {/* Dropdown del usuario */}
                  {showUserMenu && (
                    <>
                      {/* Overlay para cerrar el menú */}
                      <div 
                        className="fixed inset-0 z-10" 
                        onClick={() => setShowUserMenu(false)}
                      ></div>
                      
                      <div className="absolute right-0 top-12 w-56 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-20 transform transition-all duration-200 ease-out">
                        {/* Información del usuario en el dropdown */}
                        <div className="px-4 py-3 border-b border-gray-100">
                          <p className="text-sm font-semibold text-gray-900">{auth.user.name}</p>
                          <p className="text-xs text-gray-500 capitalize">{auth.user.rol_principal}</p>
                          <p className="text-xs text-gray-400">{auth.user.email}</p>
                        </div>
                        
                        {/* Opciones del menú */}
                        <div className="py-1">
                          <Link
                            href="/profile"
                            className="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                            onClick={() => setShowUserMenu(false)}
                          >
                            <UserCircleIcon className="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                            👤 Mi Perfil
                          </Link>
                          
                          <Link
                            href="/configuraciones"
                            className="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                            onClick={() => setShowUserMenu(false)}
                          >
                            <Cog6ToothIcon className="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                            ⚙️ Configuraciones
                          </Link>
                          
                          {/* Opciones solo para Owner */}
                          {userRole === 'owner' && (
                            <>
                              <Link
                                href="/sucursales"
                                className="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                                onClick={() => setShowUserMenu(false)}
                              >
                                <BuildingStorefrontIcon className="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                                🏬 Gestión de Sucursales
                              </Link>
                              
                              <Link
                                href="/empresa/rubros"
                                className="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                                onClick={() => setShowUserMenu(false)}
                              >
                                <TagIcon className="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                                🏷️ Gestión de Rubros
                              </Link>
                              
                              <Link
                                href="/metodos-pago"
                                className="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                                onClick={() => setShowUserMenu(false)}
                              >
                                <BanknotesIcon className="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                                💳 Métodos de Pago
                              </Link>
                              
                              <Link
                                href="/core/users"
                                className="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                                onClick={() => setShowUserMenu(false)}
                              >
                                <UsersIcon className="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                                👥 Gestión de Usuarios
                              </Link>
                              
                              <div className="border-t border-gray-100 mt-1 pt-1">
                                <Link
                                  href="/planes"
                                  className="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                                  onClick={() => setShowUserMenu(false)}
                                >
                                  <StarIcon className="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                                  ⭐ Administrar Plan
                                </Link>
                              </div>
                            </>
                          )}
                        </div>
                        
                        <div className="border-t border-gray-100">
                          <button
                            onClick={handleLogout}
                            className="group flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                          >
                            <ArrowRightOnRectangleIcon className="mr-3 h-4 w-4 text-red-400 group-hover:text-red-500" />
                            🚪 Cerrar sesión
                          </button>
                        </div>
                      </div>
                    </>
                  )}
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Multi-Tenant Header - Controlado por estado showTenantHeader */}
        {showTenantHeader && canAccessTenantSelector(auth.user.rol_principal) && (
          <MultiTenantHeader
            user={auth.user}
            empresa={empresa}
            sucursal={sucursal}
            empresasDisponibles={empresas_disponibles || []}
            sucursalesDisponibles={sucursales_disponibles || []}
          />
        )}

        <main className="py-10">
          <div className="px-4 sm:px-6 lg:px-8">
            {/* Flash messages */}
            {flash && (
              <>
                {flash.success && (
                  <div className="mb-6 rounded-md p-4 bg-green-50 border border-green-200 text-green-800">
                    {flash.success}
                  </div>
                )}
                {flash.error && (
                  <div className="mb-6 rounded-md p-4 bg-red-50 border border-red-200 text-red-800">
                    {flash.error}
                  </div>
                )}
                {flash.warning && (
                  <div className="mb-6 rounded-md p-4 bg-yellow-50 border border-yellow-200 text-yellow-800">
                    {flash.warning}
                  </div>
                )}
                {flash.message && (
                  <div className="mb-6 rounded-md p-4 bg-blue-50 border border-blue-200 text-blue-800">
                    {flash.message}
                  </div>
                )}
              </>
            )}

            {header && (
              <header className="mb-8">
                {header}
              </header>
            )}

            {children}
          </div>
        </main>
      </div>
    </div>
  );
}
