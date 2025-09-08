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
  ShoppingBagIcon
} from '@heroicons/react/24/outline';
import { clsx } from 'clsx';

interface AuthenticatedLayoutProps {
  children: React.ReactNode;
  header?: React.ReactNode;
  title?: string;
}

// Interfaces for page props
interface User {
  id: number;
  name: string;
  email: string;
  empresa_id?: number;
  sucursal_id?: number;
  rol_principal?: string;
  role?: string;
}

interface Empresa {
  id: number;
  nombre: string;
  logo?: string;
}

interface Sucursal {
  id: number;
  nombre: string;
}

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

// Función para generar navegación basada en roles
const getNavigationByRole = (userRole: string) => {
  // Módulos base que todos los usuarios pueden ver
  const baseModules = [
    { 
      name: 'Dashboard', 
      href: '/dashboard', 
      icon: ChartBarIcon,
      current: false 
    }
  ];

  // Módulos específicos por rol
  const modules = {
    // OWNER: Acceso completo a todos los módulos
    owner: [
      ...baseModules,
      { 
        name: 'POS', 
        href: '/pos', 
        icon: ShoppingCartIcon,
        current: false,
        badge: 'Principal'
      },
      { 
        name: 'Ventas', 
        href: '/ventas', 
        icon: ShoppingCartIcon,
        current: false 
      },
      { 
        name: 'Productos', 
        href: '/productos', 
        icon: CubeIcon,
        current: false 
      },
      { 
        name: 'Inventario', 
        href: '/inventario', 
        icon: BuildingStorefrontIcon,
        current: false 
      },
      { 
        name: 'Clientes', 
        href: '/clientes', 
        icon: UsersIcon,
        current: false 
      },
      { 
        name: 'Compras', 
        href: '/compras', 
        icon: ShoppingBagIcon,
        current: false 
      },
      { 
        name: 'Reportes', 
        href: '/reportes', 
        icon: DocumentChartBarIcon,
        current: false 
      },
      { 
        name: 'Configuraciones', 
        href: '/configuraciones', 
        icon: Cog6ToothIcon,
        current: false,
        badge: 'Admin'
      }
    ],

    // ADMIN: Sin acceso a configuraciones del sistema
    admin: [
      ...baseModules,
      { 
        name: 'POS', 
        href: '/pos', 
        icon: ShoppingCartIcon,
        current: false 
      },
      { 
        name: 'Ventas', 
        href: '/ventas', 
        icon: ShoppingCartIcon,
        current: false 
      },
      { 
        name: 'Productos', 
        href: '/productos', 
        icon: CubeIcon,
        current: false 
      },
      { 
        name: 'Inventario', 
        href: '/inventario', 
        icon: BuildingStorefrontIcon,
        current: false 
      },
      { 
        name: 'Clientes', 
        href: '/clientes', 
        icon: UsersIcon,
        current: false 
      },
      { 
        name: 'Reportes', 
        href: '/reportes', 
        icon: DocumentChartBarIcon,
        current: false,
        badge: 'Sucursal'
      }
    ],

    // VENDEDOR/CAJERO: Enfoque en ventas y atención al cliente
    vendedor: [
      ...baseModules,
      { 
        name: 'POS', 
        href: '/pos', 
        icon: ShoppingCartIcon,
        current: false,
        badge: 'Principal'
      },
      { 
        name: 'Ventas', 
        href: '/ventas', 
        icon: ShoppingCartIcon,
        current: false 
      },
      { 
        name: 'Productos', 
        href: '/productos', 
        icon: CubeIcon,
        current: false,
        badge: 'Solo lectura'
      },
      { 
        name: 'Clientes', 
        href: '/clientes', 
        icon: UsersIcon,
        current: false 
      }
    ],

    // CAJERO: Similar a vendedor pero más enfocado en POS
    cajero: [
      ...baseModules,
      { 
        name: 'POS', 
        href: '/pos', 
        icon: ShoppingCartIcon,
        current: false,
        badge: 'Principal'
      },
      { 
        name: 'Ventas', 
        href: '/ventas', 
        icon: ShoppingCartIcon,
        current: false 
      },
      { 
        name: 'Productos', 
        href: '/productos', 
        icon: CubeIcon,
        current: false,
        badge: 'Consulta'
      },
      { 
        name: 'Clientes', 
        href: '/clientes', 
        icon: UsersIcon,
        current: false 
      }
    ],

    // ALMACENERO: Enfoque en inventario y productos
    almacenero: [
      ...baseModules,
      { 
        name: 'Productos', 
        href: '/productos', 
        icon: CubeIcon,
        current: false,
        badge: 'Gestión'
      },
      { 
        name: 'Inventario', 
        href: '/inventario', 
        icon: BuildingStorefrontIcon,
        current: false,
        badge: 'Principal'
      },
      { 
        name: 'Reportes', 
        href: '/reportes', 
        icon: DocumentChartBarIcon,
        current: false,
        badge: 'Inventario'
      }
    ]
  };

  // Retornar módulos específicos del rol, o módulos básicos si el rol no existe
  return modules[userRole as keyof typeof modules] || modules.vendedor;
};

export default function AuthenticatedLayout({ 
  children, 
  header, 
  title 
}: AuthenticatedLayoutProps) {
  const { auth, empresa, sucursal, empresas_disponibles, sucursales_disponibles, flash } = usePage<PageProps>().props;
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [showUserMenu, setShowUserMenu] = useState(false);

  // Generar navegación dinámica basada en el rol del usuario
  const navigation = getNavigationByRole(auth.user.rol_principal || 'vendedor');

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
                      {navigation.map((item) => (
                        <li key={item.name}>
                          <Link
                            href={item.href}
                            className={clsx(
                              item.current
                                ? 'bg-red-700 text-white'
                                : 'text-red-200 hover:text-white hover:bg-red-700',
                              'group flex items-center justify-between rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200'
                            )}
                          >
                            <div className="flex items-center gap-x-3">
                              <item.icon className="h-6 w-6 shrink-0" />
                              {item.name}
                            </div>
                            {(item as any).badge && (
                              <span className="inline-flex items-center rounded-full bg-amber-400 px-2 py-0.5 text-xs font-medium text-red-800">
                                {(item as any).badge}
                              </span>
                            )}
                          </Link>
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
                  {navigation.map((item) => (
                    <li key={item.name}>
                      <Link
                        href={item.href}
                        className={clsx(
                          item.current
                            ? 'bg-red-700 text-white'
                            : 'text-red-200 hover:text-white hover:bg-red-700',
                          'group flex items-center justify-between rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200'
                        )}
                      >
                        <div className="flex items-center gap-x-3">
                          <item.icon className="h-6 w-6 shrink-0" />
                          {item.name}
                        </div>
                        {(item as any).badge && (
                          <span className="inline-flex items-center rounded-full bg-amber-400 px-2 py-0.5 text-xs font-medium text-red-800">
                            {(item as any).badge}
                          </span>
                        )}
                      </Link>
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
                    {empresa && (
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
                            <svg className="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Ver perfil
                          </Link>
                          
                          <Link
                            href="/settings"
                            className="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                            onClick={() => setShowUserMenu(false)}
                          >
                            <svg className="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Configuración
                          </Link>
                        </div>
                        
                        <div className="border-t border-gray-100">
                          <button
                            onClick={handleLogout}
                            className="group flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                          >
                            <svg className="mr-3 h-4 w-4 text-red-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Cerrar sesión
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
