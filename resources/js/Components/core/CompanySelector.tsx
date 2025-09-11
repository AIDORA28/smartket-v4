import React, { Fragment } from 'react';
import { Menu, Transition } from '@headlessui/react';
import { ChevronDownIcon, BuildingOfficeIcon } from '@heroicons/react/24/outline';
import { CompanySelectorProps } from '@/Types/core';
import { router } from '@inertiajs/react';

declare function route(name: string, params?: any): string;

const CompanyIcon = () => (
  <BuildingOfficeIcon className="w-5 h-5 text-gray-400" />
);

const LoadingSpinner = () => (
  <div className="animate-spin rounded-full h-4 w-4 border-2 border-red-600 border-t-transparent"></div>
);

const CompanyItem = ({ 
  empresa, 
  isActive, 
  onClick 
}: { 
  empresa: any; 
  isActive: boolean; 
  onClick: () => void; 
}) => (
  <Menu.Item>
    {({ active }) => (
      <button
        onClick={onClick}
        className={`${
          active ? 'bg-red-50 text-red-900' : 'text-gray-700'
        } ${
          isActive ? 'bg-red-100 text-red-800 font-medium' : ''
        } group flex w-full items-center px-4 py-2 text-sm transition-colors`}
      >
        <CompanyIcon />
        <div className="ml-3 flex-1 text-left">
          <div className="font-medium">{empresa.nombre}</div>
          {empresa.plan && (
            <div className="text-xs text-gray-500">Plan: {empresa.plan.nombre}</div>
          )}
        </div>
        {isActive && (
          <div className="w-2 h-2 bg-red-600 rounded-full ml-2"></div>
        )}
      </button>
    )}
  </Menu.Item>
);

export default function CompanySelector({ 
  currentEmpresa, 
  empresasDisponibles, 
  onEmpresaChange, 
  isLoading = false 
}: CompanySelectorProps) {
  
  const handleEmpresaChange = (empresaId: number) => {
    if (onEmpresaChange) {
      onEmpresaChange(empresaId);
    } else {
      // Default behavior: navigate to switch company
      router.post(route('core.switch-empresa'), {
        empresa_id: empresaId
      });
    }
  };

  if (!currentEmpresa) {
    return (
      <div className="flex items-center px-3 py-2 text-sm text-gray-500">
        <CompanyIcon />
        <span className="ml-2">Sin empresa</span>
      </div>
    );
  }

  return (
    <Menu as="div" className="relative inline-block text-left">
      <div>
        <Menu.Button className="inline-flex w-full justify-between items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 min-w-48">
          <div className="flex items-center">
            <CompanyIcon />
            <div className="ml-2 text-left">
              <div className="font-medium">{currentEmpresa.nombre}</div>
              {currentEmpresa.plan && (
                <div className="text-xs text-gray-500">Plan: {currentEmpresa.plan.nombre}</div>
              )}
            </div>
          </div>
          {isLoading ? (
            <LoadingSpinner />
          ) : (
            <ChevronDownIcon className="ml-2 h-4 w-4" aria-hidden="true" />
          )}
        </Menu.Button>
      </div>

      <Transition
        as={Fragment}
        enter="transition ease-out duration-100"
        enterFrom="transform opacity-0 scale-95"
        enterTo="transform opacity-100 scale-100"
        leave="transition ease-in duration-75"
        leaveFrom="transform opacity-100 scale-100"
        leaveTo="transform opacity-0 scale-95"
      >
        <Menu.Items className="absolute left-0 z-10 mt-2 w-80 origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
          <div className="py-1">
            <div className="px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wide border-b border-gray-100">
              Cambiar Empresa
            </div>
            {empresasDisponibles.map((empresa) => (
              <CompanyItem
                key={empresa.id}
                empresa={empresa}
                isActive={currentEmpresa?.id === empresa.id}
                onClick={() => !isLoading && handleEmpresaChange(empresa.id)}
              />
            ))}
          </div>
        </Menu.Items>
      </Transition>
    </Menu>
  );
}
