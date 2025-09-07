import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import { 
    ChevronDownIcon, 
    BuildingOfficeIcon,
    MapPinIcon 
} from '@heroicons/react/24/outline';

interface Empresa {
    id: number;
    nombre: string;
    nit: string;
}

interface Sucursal {
    id: number;
    nombre: string;
    direccion: string;
    empresa_id: number;
}

interface Props {
    empresas: Empresa[];
    sucursales: Sucursal[];
    empresaActual?: Empresa;
    sucursalActual?: Sucursal;
}

export default function TenantSelector({ 
    empresas, 
    sucursales, 
    empresaActual, 
    sucursalActual 
}: Props) {
    const [showDropdown, setShowDropdown] = useState(false);

    const cambiarEmpresa = (empresaId: number) => {
        router.post('/tenant/switch-empresa', { empresa_id: empresaId }, {
            onSuccess: () => {
                setShowDropdown(false);
                router.visit('/dashboard'); // Refrescar contexto
            }
        });
    };

    const cambiarSucursal = (sucursalId: number) => {
        router.post('/tenant/switch-sucursal', { sucursal_id: sucursalId }, {
            onSuccess: () => {
                setShowDropdown(false);
            }
        });
    };

    return (
        <div className="relative">
            <button
                type="button"
                onClick={() => setShowDropdown(!showDropdown)}
                className="flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
                <div className="flex items-center">
                    <BuildingOfficeIcon className="h-4 w-4 mr-2 text-gray-500" />
                    <div className="text-left">
                        <div className="text-sm font-medium text-gray-900">
                            {empresaActual?.nombre || 'Sin empresa'}
                        </div>
                        {sucursalActual && (
                            <div className="text-xs text-gray-500 flex items-center">
                                <MapPinIcon className="h-3 w-3 mr-1" />
                                {sucursalActual.nombre}
                            </div>
                        )}
                    </div>
                </div>
                <ChevronDownIcon className="ml-2 h-4 w-4 text-gray-500" />
            </button>

            {showDropdown && (
                <div className="absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                    <div className="p-3 border-b border-gray-200">
                        <h3 className="text-sm font-medium text-gray-900">Seleccionar Empresa</h3>
                    </div>
                    
                    <div className="max-h-60 overflow-y-auto">
                        {empresas.map((empresa) => (
                            <div key={empresa.id}>
                                <button
                                    onClick={() => cambiarEmpresa(empresa.id)}
                                    className={`w-full px-4 py-2 text-left hover:bg-gray-50 flex items-center ${
                                        empresaActual?.id === empresa.id 
                                            ? 'bg-blue-50 text-blue-700 font-medium' 
                                            : 'text-gray-900'
                                    }`}
                                >
                                    <BuildingOfficeIcon className="h-4 w-4 mr-3 text-gray-400" />
                                    <div>
                                        <div className="text-sm font-medium">{empresa.nombre}</div>
                                        <div className="text-xs text-gray-500">{empresa.nit}</div>
                                    </div>
                                </button>
                                
                                {/* Mostrar sucursales solo para la empresa actual */}
                                {empresaActual?.id === empresa.id && sucursales.length > 0 && (
                                    <div className="ml-6 border-l border-gray-200">
                                        {sucursales.map((sucursal) => (
                                            <button
                                                key={sucursal.id}
                                                onClick={() => cambiarSucursal(sucursal.id)}
                                                className={`w-full px-4 py-2 text-left hover:bg-gray-50 flex items-center ${
                                                    sucursalActual?.id === sucursal.id 
                                                        ? 'bg-green-50 text-green-700 font-medium' 
                                                        : 'text-gray-700'
                                                }`}
                                            >
                                                <MapPinIcon className="h-3 w-3 mr-2 text-gray-400" />
                                                <div>
                                                    <div className="text-xs font-medium">{sucursal.nombre}</div>
                                                    <div className="text-xs text-gray-500 truncate">
                                                        {sucursal.direccion}
                                                    </div>
                                                </div>
                                            </button>
                                        ))}
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                    
                    {empresas.length === 0 && (
                        <div className="p-4 text-center text-sm text-gray-500">
                            No hay empresas disponibles
                        </div>
                    )}
                </div>
            )}

            {/* Overlay para cerrar dropdown */}
            {showDropdown && (
                <div
                    className="fixed inset-0 z-40"
                    onClick={() => setShowDropdown(false)}
                />
            )}
        </div>
    );
}
