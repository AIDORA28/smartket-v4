import React from 'react';
import { Role } from '@/Types/core';

interface RoleSelectorProps {
    selectedRole: string;
    roles: Role[];
    onRoleChange: (role: string) => void;
    disabled?: boolean;
    showDescriptions?: boolean;
}

const RoleSelector: React.FC<RoleSelectorProps> = ({
    selectedRole,
    roles,
    onRoleChange,
    disabled = false,
    showDescriptions = true,
}) => {
    const getRoleColor = (roleKey: string): string => {
        const colors = {
            owner: 'bg-purple-100 text-purple-800 border-purple-200 ring-purple-500',
            admin: 'bg-blue-100 text-blue-800 border-blue-200 ring-blue-500',
            gerente: 'bg-indigo-100 text-indigo-800 border-indigo-200 ring-indigo-500',
            subgerente: 'bg-cyan-100 text-cyan-800 border-cyan-200 ring-cyan-500',
            vendedor: 'bg-green-100 text-green-800 border-green-200 ring-green-500',
            cajero: 'bg-yellow-100 text-yellow-800 border-yellow-200 ring-yellow-500',
            almacenero: 'bg-orange-100 text-orange-800 border-orange-200 ring-orange-500',
            contador: 'bg-pink-100 text-pink-800 border-pink-200 ring-pink-500',
        };
        return colors[roleKey as keyof typeof colors] || 'bg-gray-100 text-gray-800 border-gray-200 ring-gray-500';
    };

    const getRoleDescription = (roleKey: string): string => {
        const descriptions = {
            owner: 'Acceso completo al sistema, configuraciones y gestión de usuarios',
            admin: 'Gestión general con permisos administrativos avanzados',
            gerente: 'Supervisión de operaciones, personal y toma de decisiones',
            subgerente: 'Asistencia en gestión, supervisión de equipos',
            vendedor: 'Gestión de ventas, atención al cliente y productos',
            cajero: 'Manejo de caja, transacciones y cobranzas',
            almacenero: 'Gestión de inventario, stock y movimientos',
            contador: 'Gestión financiera, contable y reportes',
        };
        return descriptions[roleKey as keyof typeof descriptions] || 'Rol personalizado con permisos específicos';
    };

    const getRoleIcon = (roleKey: string): string => {
        const icons = {
            owner: '👑',
            admin: '🛡️',
            gerente: '🎯',
            subgerente: '📊',
            vendedor: '🤝',
            cajero: '💰',
            almacenero: '📦',
            contador: '📈',
        };
        return icons[roleKey as keyof typeof icons] || '👤';
    };

    const getPermissionLevel = (roleKey: string): { level: number; label: string; color: string } => {
        const levels = {
            owner: { level: 10, label: 'Máximo', color: 'text-purple-600' },
            admin: { level: 8, label: 'Alto', color: 'text-blue-600' },
            gerente: { level: 7, label: 'Alto', color: 'text-indigo-600' },
            subgerente: { level: 6, label: 'Medio-Alto', color: 'text-cyan-600' },
            vendedor: { level: 5, label: 'Medio', color: 'text-green-600' },
            cajero: { level: 4, label: 'Medio', color: 'text-yellow-600' },
            almacenero: { level: 4, label: 'Medio', color: 'text-orange-600' },
            contador: { level: 6, label: 'Medio-Alto', color: 'text-pink-600' },
        };
        return levels[roleKey as keyof typeof levels] || { level: 3, label: 'Básico', color: 'text-gray-600' };
    };

    return (
        <div className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                {roles.map((role) => {
                    const isSelected = selectedRole === role.key;
                    const roleColors = getRoleColor(role.key);
                    const permissionLevel = getPermissionLevel(role.key);
                    
                    return (
                        <div
                            key={role.key}
                            className={`relative rounded-lg border p-4 cursor-pointer transition-all duration-200 ${
                                disabled 
                                    ? 'opacity-50 cursor-not-allowed'
                                    : isSelected
                                        ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500 ring-opacity-20 shadow-md'
                                        : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50 hover:shadow-sm'
                            }`}
                            onClick={() => !disabled && onRoleChange(role.key)}
                        >
                            {/* Indicador de Premium */}
                            {role.isPremium && (
                                <div className="absolute top-2 right-2">
                                    <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gradient-to-r from-yellow-400 to-orange-500 text-white shadow-sm">
                                        ✨ PREMIUM
                                    </span>
                                </div>
                            )}

                            <div className="flex items-start">
                                <div className="flex-shrink-0">
                                    <input
                                        type="radio"
                                        name="role_selector"
                                        value={role.key}
                                        checked={isSelected}
                                        onChange={() => !disabled && onRoleChange(role.key)}
                                        disabled={disabled}
                                        className="mt-1 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 disabled:opacity-50"
                                    />
                                </div>
                                <div className="ml-3 flex-1">
                                    {/* Header del Rol */}
                                    <div className="flex items-center mb-2">
                                        <span className="text-lg mr-2" role="img" aria-label={`${role.name} icon`}>
                                            {getRoleIcon(role.key)}
                                        </span>
                                        <div className="flex flex-col">
                                            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${roleColors}`}>
                                                {role.name}
                                            </span>
                                            <span className={`text-xs font-medium mt-1 ${permissionLevel.color}`}>
                                                Nivel: {permissionLevel.label}
                                            </span>
                                        </div>
                                    </div>

                                    {/* Descripción */}
                                    {showDescriptions && (
                                        <p className="text-xs text-gray-600 leading-relaxed">
                                            {getRoleDescription(role.key)}
                                        </p>
                                    )}

                                    {/* Barra de nivel de permisos */}
                                    <div className="mt-3">
                                        <div className="flex justify-between items-center mb-1">
                                            <span className="text-xs text-gray-500">Permisos</span>
                                            <span className="text-xs text-gray-500">{permissionLevel.level}/10</span>
                                        </div>
                                        <div className="w-full bg-gray-200 rounded-full h-1.5">
                                            <div 
                                                className={`h-1.5 rounded-full transition-all duration-300 ${
                                                    permissionLevel.level >= 8 ? 'bg-gradient-to-r from-purple-500 to-blue-500' :
                                                    permissionLevel.level >= 6 ? 'bg-gradient-to-r from-blue-500 to-green-500' :
                                                    permissionLevel.level >= 4 ? 'bg-gradient-to-r from-green-500 to-yellow-500' :
                                                    'bg-gradient-to-r from-yellow-500 to-orange-500'
                                                }`}
                                                style={{ width: `${(permissionLevel.level / 10) * 100}%` }}
                                            ></div>
                                        </div>
                                    </div>

                                    {/* Indicadores adicionales */}
                                    <div className="mt-2 flex items-center space-x-2">
                                        {role.key === 'owner' && (
                                            <span className="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                <svg className="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m0 0v2m0-2h2m-2 0H10m4-9a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                Máximo nivel
                                            </span>
                                        )}
                                        {['admin', 'gerente'].includes(role.key) && (
                                            <span className="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg className="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                                Administrativo
                                            </span>
                                        )}
                                        {['vendedor', 'cajero'].includes(role.key) && (
                                            <span className="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                <svg className="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                                Operativo
                                            </span>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    );
                })}
            </div>

            {/* Información adicional del rol seleccionado */}
            {selectedRole && (
                <div className="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div className="flex items-start">
                        <div className="flex-shrink-0">
                            <svg className="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div className="ml-3">
                            <h4 className="text-sm font-medium text-blue-800">
                                Rol seleccionado: {roles.find(r => r.key === selectedRole)?.name}
                            </h4>
                            <p className="mt-1 text-sm text-blue-700">
                                {getRoleDescription(selectedRole)}
                            </p>
                            <div className="mt-2">
                                <span className="text-xs text-blue-600 font-medium">
                                    Nivel de acceso: {getPermissionLevel(selectedRole).label}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default RoleSelector;
