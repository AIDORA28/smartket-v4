import React, { Fragment } from 'react';
import { Menu, Transition } from '@headlessui/react';
import { ChevronDownIcon, MapPinIcon } from '@heroicons/react/24/outline';
import { BranchSelectorProps } from '@/Types/core';
import { router } from '@inertiajs/react';

declare function route(name: string, params?: any): string;

const BranchIcon = () => (
  <MapPinIcon className="w-5 h-5 text-gray-400" />
);

const LoadingSpinner = () => (
  <div className="animate-spin rounded-full h-4 w-4 border-2 border-blue-600 border-t-transparent"></div>
);

const BranchItem = ({ 
  sucursal, 
  isActive, 
  onClick 
}: { 
  sucursal: any; 
  isActive: boolean; 
  onClick: () => void; 
}) => (
  <Menu.Item>
    {({ active }) => (
      <button
        onClick={onClick}
        className={`${
          active ? 'bg-blue-50 text-blue-900' : 'text-gray-700'
        } ${
          isActive ? 'bg-blue-100 text-blue-800 font-medium' : ''
        } group flex w-full items-center px-4 py-2 text-sm transition-colors`}
      >
        <BranchIcon />
        <div className="ml-3 flex-1 text-left">
          <div className="font-medium">{sucursal.nombre}</div>
          {sucursal.direccion && (
            <div className="text-xs text-gray-500">{sucursal.direccion}</div>
          )}
        </div>
        {isActive && (
          <div className="w-2 h-2 bg-blue-600 rounded-full ml-2"></div>
        )}
      </button>
    )}
  </Menu.Item>
);

export default function BranchSelector({ 
  currentSucursal, 
  sucursalesDisponibles, 
  onSucursalChange, 
  isLoading = false 
}: BranchSelectorProps) {
  
  const handleSucursalChange = (sucursalId: number) => {
    if (onSucursalChange) {
      onSucursalChange(sucursalId);
    } else {
      // Default behavior: navigate to switch branch
      router.post(route('core.switch-sucursal'), {
        sucursal_id: sucursalId
      });
    }
  };

  if (!currentSucursal) {
    return (
      <div className="flex items-center px-3 py-2 text-sm text-gray-500">
        <BranchIcon />
        <span className="ml-2">Sin sucursal</span>
      </div>
    );
  }

  return (
    <Menu as="div" className="relative inline-block text-left">
      <div>
        <Menu.Button className="inline-flex w-full justify-between items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 min-w-48">
          <div className="flex items-center">
            <BranchIcon />
            <div className="ml-2 text-left">
              <div className="font-medium">{currentSucursal.nombre}</div>
              {currentSucursal.direccion && (
                <div className="text-xs text-gray-500">{currentSucursal.direccion}</div>
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
              Cambiar Sucursal
            </div>
            {sucursalesDisponibles.map((sucursal) => (
              <BranchItem
                key={sucursal.id}
                sucursal={sucursal}
                isActive={currentSucursal?.id === sucursal.id}
                onClick={() => !isLoading && handleSucursalChange(sucursal.id)}
              />
            ))}
          </div>
        </Menu.Items>
      </Transition>
    </Menu>
  );
}
