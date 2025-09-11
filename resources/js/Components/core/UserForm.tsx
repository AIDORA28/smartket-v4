import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { UserFormData, Role, Empresa, Sucursal } from '@/Types/core';

interface UserFormProps {
    user?: UserFormData;
    roles: Role[];
    empresas: Empresa[];
    sucursales: Sucursal[];
    mode: 'create' | 'edit';
    onCancel: () => void;
}

const UserForm: React.FC<UserFormProps> = ({
    user,
    roles,
    empresas,
    sucursales,
    mode,
    onCancel
}) => {
    const { data, setData, post, put, processing, errors, reset } = useForm<UserFormData>({
        name: user?.name || '',
        email: user?.email || '',
        password: '',
        password_confirmation: '',
        rol_principal: user?.rol_principal || 'vendedor',
        empresa_id: user?.empresa_id || empresas[0]?.id || 1,
        sucursal_id: user?.sucursal_id || sucursales[0]?.id || 1,
        activo: user?.activo ?? true,
    });

    const [filteredSucursales, setFilteredSucursales] = useState<Sucursal[]>([]);

    // Filtrar sucursales por empresa seleccionada
    useEffect(() => {
        const empresaActual = empresas.find(e => e.id === data.empresa_id);
        if (empresaActual) {
            const sucursalesEmpresa = sucursales.filter(s => s.empresa_id === data.empresa_id);
            setFilteredSucursales(sucursalesEmpresa);
            
            // Si la sucursal actual no pertenece a la empresa, seleccionar la primera disponible
            if (!sucursalesEmpresa.find(s => s.id === data.sucursal_id)) {
                setData('sucursal_id', sucursalesEmpresa[0]?.id || 1);
            }
        }
    }, [data.empresa_id, empresas, sucursales]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        if (mode === 'create') {
            post(route('core.users.store'), {
                onSuccess: () => {
                    reset();
                    onCancel();
                }
            });
        } else {
            put(route('core.users.update', user?.id), {
                onSuccess: () => {
                    onCancel();
                }
            });
        }
    };

    const getRoleColor = (roleKey: string): string => {
        const colors = {
            owner: 'bg-purple-100 text-purple-800 border-purple-200',
            admin: 'bg-blue-100 text-blue-800 border-blue-200',
            gerente: 'bg-indigo-100 text-indigo-800 border-indigo-200',
            subgerente: 'bg-cyan-100 text-cyan-800 border-cyan-200',
            vendedor: 'bg-green-100 text-green-800 border-green-200',
            cajero: 'bg-yellow-100 text-yellow-800 border-yellow-200',
            almacenero: 'bg-orange-100 text-orange-800 border-orange-200',
            contador: 'bg-pink-100 text-pink-800 border-pink-200',
        };
        return colors[roleKey as keyof typeof colors] || 'bg-gray-100 text-gray-800 border-gray-200';
    };

    const getRoleDescription = (roleKey: string): string => {
        const descriptions = {
            owner: 'Acceso completo al sistema y configuraciones',
            admin: 'Gestión general con permisos administrativos',
            gerente: 'Supervisión de operaciones y personal',
            subgerente: 'Asistencia en gestión y supervisión',
            vendedor: 'Gestión de ventas y atención al cliente',
            cajero: 'Manejo de caja y transacciones',
            almacenero: 'Gestión de inventario y stock',
            contador: 'Gestión financiera y contable',
        };
        return descriptions[roleKey as keyof typeof descriptions] || 'Rol personalizado';
    };

    return (
        <div className="bg-white shadow-sm rounded-lg border border-gray-200">
            <div className="px-6 py-4 border-b border-gray-200">
                <div className="flex items-center justify-between">
                    <div>
                        <h3 className="text-lg font-medium text-gray-900">
                            {mode === 'create' ? 'Crear Usuario' : 'Editar Usuario'}
                        </h3>
                        <p className="mt-1 text-sm text-gray-500">
                            {mode === 'create' 
                                ? 'Complete la información del nuevo usuario'
                                : 'Modifique los datos del usuario seleccionado'
                            }
                        </p>
                    </div>
                    <button
                        type="button"
                        onClick={onCancel}
                        className="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition-colors duration-200"
                    >
                        <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <form onSubmit={handleSubmit} className="px-6 py-6 space-y-6">
                {/* Información Personal */}
                <div className="space-y-4">
                    <h4 className="text-base font-medium text-gray-900 flex items-center">
                        <svg className="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Información Personal
                    </h4>
                    
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {/* Nombre Completo */}
                        <div>
                            <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">
                                Nombre Completo *
                            </label>
                            <input
                                type="text"
                                id="name"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                className={`w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                    errors.name ? 'border-red-300 bg-red-50' : 'border-gray-300'
                                }`}
                                placeholder="Ingrese el nombre completo"
                                required
                            />
                            {errors.name && (
                                <p className="mt-1 text-sm text-red-600">{errors.name}</p>
                            )}
                        </div>

                        {/* Email */}
                        <div>
                            <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
                                Correo Electrónico *
                            </label>
                            <input
                                type="email"
                                id="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                className={`w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                    errors.email ? 'border-red-300 bg-red-50' : 'border-gray-300'
                                }`}
                                placeholder="usuario@empresa.com"
                                required
                            />
                            {errors.email && (
                                <p className="mt-1 text-sm text-red-600">{errors.email}</p>
                            )}
                        </div>
                    </div>

                    {/* Contraseñas */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-1">
                                {mode === 'create' ? 'Contraseña *' : 'Nueva Contraseña (opcional)'}
                            </label>
                            <input
                                type="password"
                                id="password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                className={`w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                    errors.password ? 'border-red-300 bg-red-50' : 'border-gray-300'
                                }`}
                                placeholder="••••••••"
                                required={mode === 'create'}
                            />
                            {errors.password && (
                                <p className="mt-1 text-sm text-red-600">{errors.password}</p>
                            )}
                        </div>

                        <div>
                            <label htmlFor="password_confirmation" className="block text-sm font-medium text-gray-700 mb-1">
                                Confirmar Contraseña {mode === 'create' && '*'}
                            </label>
                            <input
                                type="password"
                                id="password_confirmation"
                                value={data.password_confirmation}
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                className={`w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                    errors.password_confirmation ? 'border-red-300 bg-red-50' : 'border-gray-300'
                                }`}
                                placeholder="••••••••"
                                required={mode === 'create' || data.password !== ''}
                            />
                            {errors.password_confirmation && (
                                <p className="mt-1 text-sm text-red-600">{errors.password_confirmation}</p>
                            )}
                        </div>
                    </div>
                </div>

                {/* Rol y Permisos */}
                <div className="space-y-4">
                    <h4 className="text-base font-medium text-gray-900 flex items-center">
                        <svg className="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Rol y Permisos
                    </h4>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-3">
                            Rol Principal *
                        </label>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            {roles.map((role) => {
                                const isSelected = data.rol_principal === role.key;
                                return (
                                    <div
                                        key={role.key}
                                        className={`relative rounded-lg border p-4 cursor-pointer transition-all duration-200 ${
                                            isSelected
                                                ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500 ring-opacity-20'
                                                : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50'
                                        }`}
                                        onClick={() => setData('rol_principal', role.key)}
                                    >
                                        <div className="flex items-start">
                                            <div className="flex-shrink-0">
                                                <input
                                                    type="radio"
                                                    name="rol_principal"
                                                    value={role.key}
                                                    checked={isSelected}
                                                    onChange={() => setData('rol_principal', role.key)}
                                                    className="mt-1 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                                />
                                            </div>
                                            <div className="ml-3 flex-1">
                                                <div className="flex items-center">
                                                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${getRoleColor(role.key)}`}>
                                                        {role.name}
                                                    </span>
                                                    {role.isPremium && (
                                                        <span className="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gold-100 text-gold-800 border border-gold-200">
                                                            PREMIUM
                                                        </span>
                                                    )}
                                                </div>
                                                <p className="mt-1 text-xs text-gray-500">
                                                    {getRoleDescription(role.key)}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                        {errors.rol_principal && (
                            <p className="mt-2 text-sm text-red-600">{errors.rol_principal}</p>
                        )}
                    </div>
                </div>

                {/* Asignación de Empresa y Sucursal */}
                <div className="space-y-4">
                    <h4 className="text-base font-medium text-gray-900 flex items-center">
                        <svg className="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Asignación Organizacional
                    </h4>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {/* Empresa */}
                        <div>
                            <label htmlFor="empresa_id" className="block text-sm font-medium text-gray-700 mb-1">
                                Empresa *
                            </label>
                            <select
                                id="empresa_id"
                                value={data.empresa_id}
                                onChange={(e) => setData('empresa_id', parseInt(e.target.value))}
                                className={`w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                    errors.empresa_id ? 'border-red-300 bg-red-50' : 'border-gray-300'
                                }`}
                                required
                            >
                                {empresas.map((empresa) => (
                                    <option key={empresa.id} value={empresa.id}>
                                        {empresa.nombre}
                                    </option>
                                ))}
                            </select>
                            {errors.empresa_id && (
                                <p className="mt-1 text-sm text-red-600">{errors.empresa_id}</p>
                            )}
                        </div>

                        {/* Sucursal */}
                        <div>
                            <label htmlFor="sucursal_id" className="block text-sm font-medium text-gray-700 mb-1">
                                Sucursal *
                            </label>
                            <select
                                id="sucursal_id"
                                value={data.sucursal_id}
                                onChange={(e) => setData('sucursal_id', parseInt(e.target.value))}
                                className={`w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                    errors.sucursal_id ? 'border-red-300 bg-red-50' : 'border-gray-300'
                                }`}
                                required
                            >
                                {filteredSucursales.map((sucursal) => (
                                    <option key={sucursal.id} value={sucursal.id}>
                                        {sucursal.nombre}
                                    </option>
                                ))}
                            </select>
                            {errors.sucursal_id && (
                                <p className="mt-1 text-sm text-red-600">{errors.sucursal_id}</p>
                            )}
                        </div>
                    </div>
                </div>

                {/* Estado */}
                <div className="space-y-4">
                    <h4 className="text-base font-medium text-gray-900 flex items-center">
                        <svg className="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Estado del Usuario
                    </h4>

                    <div className="flex items-center">
                        <label className="relative inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                checked={data.activo}
                                onChange={(e) => setData('activo', e.target.checked)}
                                className="sr-only peer"
                            />
                            <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span className="ml-3 text-sm font-medium text-gray-700">
                                Usuario {data.activo ? 'activo' : 'inactivo'}
                            </span>
                        </label>
                    </div>
                    <p className="text-sm text-gray-500">
                        {data.activo 
                            ? 'El usuario puede acceder al sistema y realizar sus funciones.' 
                            : 'El usuario no podrá acceder al sistema hasta ser reactivado.'
                        }
                    </p>
                </div>

                {/* Botones de Acción */}
                <div className="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button
                        type="button"
                        onClick={onCancel}
                        className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        disabled={processing}
                        className="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                    >
                        {processing ? (
                            <div className="flex items-center">
                                <svg className="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                    <path className="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Procesando...
                            </div>
                        ) : (
                            mode === 'create' ? 'Crear Usuario' : 'Actualizar Usuario'
                        )}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default UserForm;
