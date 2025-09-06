import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { 
  ChartBarIcon,
  CubeIcon,
  ShoppingCartIcon,
  UsersIcon,
  BuildingStorefrontIcon,
  Cog6ToothIcon,
  ArrowRightOnRectangleIcon,
  Bars3Icon,
  XMarkIcon
} from '@heroicons/react/24/outline';
import { clsx } from 'clsx';
import { User, Tenant } from '@/Types';

interface AuthenticatedLayoutProps {
  children: React.ReactNode;
  header?: React.ReactNode;
  title?: string;
}

// PageProps local para evitar conflictos con Inertia
interface LocalPageProps {
  auth: {
    user: User;
    tenant: Tenant;
  };
  flash?: {
    type: 'success' | 'error' | 'warning' | 'info';
    message: string;
  };
  [key: string]: any;
}

const navigation = [
  { 
    name: 'Dashboard', 
    href: '/dashboard', 
    icon: ChartBarIcon,
    current: false 
  },
  { 
    name: 'Productos', 
    href: '/productos', 
    icon: CubeIcon,
    current: false 
  },
  { 
    name: 'Ventas', 
    href: '/ventas', 
    icon: ShoppingCartIcon,
    current: false 
  },
  { 
    name: 'Clientes', 
    href: '/clientes', 
    icon: UsersIcon,
    current: false 
  },
  { 
    name: 'Inventario', 
    href: '/inventario', 
    icon: BuildingStorefrontIcon,
    current: false 
  },
  { 
    name: 'Configuración', 
    href: '/configuracion', 
    icon: Cog6ToothIcon,
    current: false 
  }
];

export default function AuthenticatedLayout({ 
  children, 
  header, 
  title 
}: AuthenticatedLayoutProps) {
  const { auth, flash } = usePage<LocalPageProps>().props;
  const [sidebarOpen, setSidebarOpen] = useState(false);

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

            <div className="flex grow flex-col gap-y-5 overflow-y-auto bg-indigo-600 px-6 pb-4 ring-1 ring-white/10">
              <div className="flex h-16 shrink-0 items-center">
                <h1 className="text-xl font-bold text-white">SmartKet v4</h1>
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
                                ? 'bg-indigo-700 text-white'
                                : 'text-indigo-200 hover:text-white hover:bg-indigo-700',
                              'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold'
                            )}
                          >
                            <item.icon className="h-6 w-6 shrink-0" />
                            {item.name}
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
        <div className="flex grow flex-col gap-y-5 overflow-y-auto bg-indigo-600 px-6 pb-4">
          <div className="flex h-16 shrink-0 items-center">
            <h1 className="text-xl font-bold text-white">SmartKet v4</h1>
            <span className="ml-2 text-xs text-indigo-200">
              {auth.tenant.name}
            </span>
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
                            ? 'bg-indigo-700 text-white'
                            : 'text-indigo-200 hover:text-white hover:bg-indigo-700',
                          'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition-colors duration-200'
                        )}
                      >
                        <item.icon className="h-6 w-6 shrink-0" />
                        {item.name}
                      </Link>
                    </li>
                  ))}
                </ul>
              </li>
              
              <li className="mt-auto">
                <Link
                  href="/logout"
                  method="post"
                  className="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-indigo-200 hover:bg-indigo-700 hover:text-white"
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
        <div className="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
          <button
            type="button"
            className="-m-2.5 p-2.5 text-gray-700 lg:hidden"
            onClick={() => setSidebarOpen(true)}
          >
            <Bars3Icon className="h-6 w-6" />
          </button>

          <div className="h-6 w-px bg-gray-200 lg:hidden" />

          <div className="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
            <div className="relative flex flex-1 items-center">
              {title && (
                <h1 className="text-2xl font-semibold leading-6 text-gray-900">
                  {title}
                </h1>
              )}
            </div>
            
            <div className="flex items-center gap-x-4 lg:gap-x-6">
              <div className="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" />
              
              <div className="relative">
                <span className="text-sm font-medium text-gray-700">
                  {auth.user.name}
                </span>
                <p className="text-xs text-gray-500">{auth.user.email}</p>
              </div>
            </div>
          </div>
        </div>

        <main className="py-10">
          <div className="px-4 sm:px-6 lg:px-8">
            {/* Flash messages */}
            {flash && (
              <div className={clsx(
                'mb-6 rounded-md p-4',
                flash.type === 'success' && 'bg-green-50 border border-green-200 text-green-800',
                flash.type === 'error' && 'bg-red-50 border border-red-200 text-red-800',
                flash.type === 'warning' && 'bg-yellow-50 border border-yellow-200 text-yellow-800',
                flash.type === 'info' && 'bg-blue-50 border border-blue-200 text-blue-800'
              )}>
                {flash.message}
              </div>
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
