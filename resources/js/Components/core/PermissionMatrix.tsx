import React from 'react';
import { Role } from '@/Types/core';

interface Permission {
    key: string;
    name: string;
    description: string;
    category: string;
}

interface PermissionMatrixProps {
    selectedRole: string;
    rolePermissions: string[];
    onPermissionChange?: (permissions: string[]) => void;
    readonly?: boolean;
}

const PermissionMatrix: React.FC<PermissionMatrixProps> = ({
    selectedRole,
    rolePermissions,
    onPermissionChange,
    readonly = false,
}) => {
    // Definición completa de permisos del sistema
    const permissions: Permission[] = [
        // Dashboard y Sistema
        { key: 'dashboard.view', name: 'Ver Dashboard', description: 'Acceso al panel principal', category: 'Sistema' },
        { key: 'system.settings', name: 'Configuración Sistema', description: 'Modificar configuraciones generales', category: 'Sistema' },
        { key: 'system.logs', name: 'Ver Logs', description: 'Acceso a logs del sistema', category: 'Sistema' },
        
        // Gestión de Usuarios
        { key: 'users.view', name: 'Ver Usuarios', description: 'Listar usuarios del sistema', category: 'Usuarios' },
        { key: 'users.create', name: 'Crear Usuarios', description: 'Agregar nuevos usuarios', category: 'Usuarios' },
        { key: 'users.edit', name: 'Editar Usuarios', description: 'Modificar información de usuarios', category: 'Usuarios' },
        { key: 'users.delete', name: 'Eliminar Usuarios', description: 'Desactivar o eliminar usuarios', category: 'Usuarios' },
        { key: 'users.roles', name: 'Gestionar Roles', description: 'Asignar y modificar roles', category: 'Usuarios' },
        
        // Empresas y Sucursales
        { key: 'companies.view', name: 'Ver Empresas', description: 'Listar empresas del sistema', category: 'Empresas' },
        { key: 'companies.edit', name: 'Editar Empresas', description: 'Modificar información empresarial', category: 'Empresas' },
        { key: 'branches.view', name: 'Ver Sucursales', description: 'Listar sucursales', category: 'Empresas' },
        { key: 'branches.create', name: 'Crear Sucursales', description: 'Agregar nuevas sucursales', category: 'Empresas' },
        { key: 'branches.edit', name: 'Editar Sucursales', description: 'Modificar sucursales', category: 'Empresas' },
        
        // Productos e Inventario
        { key: 'products.view', name: 'Ver Productos', description: 'Listar productos', category: 'Inventario' },
        { key: 'products.create', name: 'Crear Productos', description: 'Agregar nuevos productos', category: 'Inventario' },
        { key: 'products.edit', name: 'Editar Productos', description: 'Modificar información de productos', category: 'Inventario' },
        { key: 'products.delete', name: 'Eliminar Productos', description: 'Desactivar productos', category: 'Inventario' },
        { key: 'inventory.view', name: 'Ver Inventario', description: 'Consultar stock y movimientos', category: 'Inventario' },
        { key: 'inventory.adjust', name: 'Ajustar Stock', description: 'Realizar ajustes de inventario', category: 'Inventario' },
        { key: 'inventory.transfer', name: 'Transferencias', description: 'Transferir productos entre sucursales', category: 'Inventario' },
        
        // Ventas y POS
        { key: 'sales.view', name: 'Ver Ventas', description: 'Consultar historial de ventas', category: 'Ventas' },
        { key: 'sales.create', name: 'Realizar Ventas', description: 'Procesar ventas en POS', category: 'Ventas' },
        { key: 'sales.edit', name: 'Editar Ventas', description: 'Modificar ventas existentes', category: 'Ventas' },
        { key: 'sales.cancel', name: 'Anular Ventas', description: 'Cancelar o anular ventas', category: 'Ventas' },
        { key: 'pos.access', name: 'Acceso POS', description: 'Usar el sistema punto de venta', category: 'Ventas' },
        
        // Caja y Pagos
        { key: 'cash.view', name: 'Ver Caja', description: 'Consultar estado de caja', category: 'Caja' },
        { key: 'cash.open', name: 'Abrir Caja', description: 'Iniciar sesión de caja', category: 'Caja' },
        { key: 'cash.close', name: 'Cerrar Caja', description: 'Finalizar sesión de caja', category: 'Caja' },
        { key: 'cash.count', name: 'Arqueo de Caja', description: 'Realizar conteo de efectivo', category: 'Caja' },
        
        // Compras y Proveedores
        { key: 'purchases.view', name: 'Ver Compras', description: 'Consultar historial de compras', category: 'Compras' },
        { key: 'purchases.create', name: 'Crear Compras', description: 'Registrar nuevas compras', category: 'Compras' },
        { key: 'suppliers.view', name: 'Ver Proveedores', description: 'Listar proveedores', category: 'Compras' },
        { key: 'suppliers.create', name: 'Crear Proveedores', description: 'Agregar nuevos proveedores', category: 'Compras' },
        
        // Clientes
        { key: 'customers.view', name: 'Ver Clientes', description: 'Listar clientes', category: 'Clientes' },
        { key: 'customers.create', name: 'Crear Clientes', description: 'Agregar nuevos clientes', category: 'Clientes' },
        { key: 'customers.edit', name: 'Editar Clientes', description: 'Modificar información de clientes', category: 'Clientes' },
        
        // Reportes
        { key: 'reports.view', name: 'Ver Reportes', description: 'Acceso a reportes básicos', category: 'Reportes' },
        { key: 'reports.advanced', name: 'Reportes Avanzados', description: 'Reportes detallados y analytics', category: 'Reportes' },
        { key: 'reports.export', name: 'Exportar Datos', description: 'Exportar reportes a Excel/PDF', category: 'Reportes' },
    ];

    // Agrupación de permisos por categoría
    const permissionsByCategory = permissions.reduce((acc, permission) => {
        if (!acc[permission.category]) {
            acc[permission.category] = [];
        }
        acc[permission.category].push(permission);
        return acc;
    }, {} as Record<string, Permission[]>);

    // Permisos predefinidos por rol
    const rolePermissionSets: Record<string, string[]> = {
        owner: permissions.map(p => p.key), // Todos los permisos
        admin: [
            'dashboard.view', 'system.settings', 'system.logs',
            'users.view', 'users.create', 'users.edit', 'users.roles',
            'companies.view', 'companies.edit', 'branches.view', 'branches.create', 'branches.edit',
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'inventory.view', 'inventory.adjust', 'inventory.transfer',
            'sales.view', 'sales.create', 'sales.edit', 'sales.cancel',
            'cash.view', 'cash.open', 'cash.close', 'cash.count',
            'purchases.view', 'purchases.create', 'suppliers.view', 'suppliers.create',
            'customers.view', 'customers.create', 'customers.edit',
            'reports.view', 'reports.advanced', 'reports.export'
        ],
        gerente: [
            'dashboard.view',
            'users.view',
            'branches.view',
            'products.view', 'products.create', 'products.edit',
            'inventory.view', 'inventory.adjust',
            'sales.view', 'sales.create', 'sales.edit',
            'cash.view', 'cash.open', 'cash.close', 'cash.count',
            'purchases.view', 'purchases.create', 'suppliers.view',
            'customers.view', 'customers.create', 'customers.edit',
            'reports.view', 'reports.advanced'
        ],
        subgerente: [
            'dashboard.view',
            'products.view', 'products.create', 'products.edit',
            'inventory.view', 'inventory.adjust',
            'sales.view', 'sales.create', 'sales.edit',
            'cash.view', 'cash.count',
            'customers.view', 'customers.create', 'customers.edit',
            'reports.view'
        ],
        vendedor: [
            'dashboard.view',
            'products.view',
            'inventory.view',
            'sales.view', 'sales.create', 'pos.access',
            'customers.view', 'customers.create', 'customers.edit'
        ],
        cajero: [
            'dashboard.view',
            'products.view',
            'sales.view', 'sales.create', 'pos.access',
            'cash.view', 'cash.open', 'cash.close', 'cash.count',
            'customers.view', 'customers.create'
        ],
        almacenero: [
            'dashboard.view',
            'products.view', 'products.create', 'products.edit',
            'inventory.view', 'inventory.adjust', 'inventory.transfer',
            'purchases.view', 'suppliers.view'
        ],
        contador: [
            'dashboard.view',
            'sales.view',
            'cash.view', 'cash.count',
            'purchases.view',
            'reports.view', 'reports.advanced', 'reports.export'
        ]
    };

    const currentPermissions = rolePermissionSets[selectedRole] || [];
    const hasPermission = (permissionKey: string) => currentPermissions.includes(permissionKey);

    const getCategoryColor = (category: string): string => {
        const colors: Record<string, string> = {
            'Sistema': 'bg-purple-50 border-purple-200',
            'Usuarios': 'bg-blue-50 border-blue-200',
            'Empresas': 'bg-green-50 border-green-200',
            'Inventario': 'bg-yellow-50 border-yellow-200',
            'Ventas': 'bg-red-50 border-red-200',
            'Caja': 'bg-indigo-50 border-indigo-200',
            'Compras': 'bg-pink-50 border-pink-200',
            'Clientes': 'bg-cyan-50 border-cyan-200',
            'Reportes': 'bg-orange-50 border-orange-200',
        };
        return colors[category] || 'bg-gray-50 border-gray-200';
    };

    const getCategoryIcon = (category: string): string => {
        const icons: Record<string, string> = {
            'Sistema': '⚙️',
            'Usuarios': '👥',
            'Empresas': '🏢',
            'Inventario': '📦',
            'Ventas': '💰',
            'Caja': '💳',
            'Compras': '🛒',
            'Clientes': '👤',
            'Reportes': '📊',
        };
        return icons[category] || '📋';
    };

    const getPermissionLevel = (permissions: string[]): { level: number; label: string; color: string } => {
        const total = permissions.length;
        const maxPermissions = permissions.length;
        const percentage = total / maxPermissions;
        
        if (percentage >= 0.9) return { level: 10, label: 'Máximo', color: 'text-purple-600' };
        if (percentage >= 0.7) return { level: 8, label: 'Alto', color: 'text-blue-600' };
        if (percentage >= 0.5) return { level: 6, label: 'Medio', color: 'text-green-600' };
        if (percentage >= 0.3) return { level: 4, label: 'Básico', color: 'text-yellow-600' };
        return { level: 2, label: 'Limitado', color: 'text-red-600' };
    };

    const permissionLevel = getPermissionLevel(currentPermissions);

    return (
        <div className="space-y-6">
            {/* Header con resumen */}
            <div className="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h3 className="text-lg font-medium text-gray-900 flex items-center">
                            <svg className="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Matriz de Permisos - {selectedRole.charAt(0).toUpperCase() + selectedRole.slice(1)}
                        </h3>
                        <p className="text-sm text-gray-600 mt-1">
                            {currentPermissions.length} de {permissions.length} permisos asignados
                        </p>
                    </div>
                    <div className="text-right">
                        <span className={`text-lg font-bold ${permissionLevel.color}`}>
                            {permissionLevel.label}
                        </span>
                        <div className="text-sm text-gray-500">Nivel de acceso</div>
                    </div>
                </div>

                {/* Barra de progreso */}
                <div className="mt-4">
                    <div className="flex justify-between items-center mb-2">
                        <span className="text-sm text-gray-600">Cobertura de permisos</span>
                        <span className="text-sm text-gray-600">
                            {Math.round((currentPermissions.length / permissions.length) * 100)}%
                        </span>
                    </div>
                    <div className="w-full bg-gray-200 rounded-full h-2">
                        <div 
                            className="h-2 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-300"
                            style={{ width: `${(currentPermissions.length / permissions.length) * 100}%` }}
                        ></div>
                    </div>
                </div>
            </div>

            {/* Matriz de permisos por categoría */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {Object.entries(permissionsByCategory).map(([category, categoryPermissions]) => {
                    const categoryHasPermissions = categoryPermissions.some(p => hasPermission(p.key));
                    const categoryPermissionCount = categoryPermissions.filter(p => hasPermission(p.key)).length;
                    
                    return (
                        <div
                            key={category}
                            className={`border rounded-lg overflow-hidden ${getCategoryColor(category)}`}
                        >
                            {/* Header de categoría */}
                            <div className="px-4 py-3 bg-white bg-opacity-50 border-b">
                                <div className="flex items-center justify-between">
                                    <h4 className="font-medium text-gray-900 flex items-center">
                                        <span className="text-lg mr-2" role="img" aria-label={category}>
                                            {getCategoryIcon(category)}
                                        </span>
                                        {category}
                                    </h4>
                                    <div className="flex items-center space-x-2">
                                        <span className="text-sm text-gray-600">
                                            {categoryPermissionCount}/{categoryPermissions.length}
                                        </span>
                                        {categoryHasPermissions && (
                                            <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg className="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                                </svg>
                                                Activo
                                            </span>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Lista de permisos */}
                            <div className="divide-y divide-gray-200">
                                {categoryPermissions.map((permission) => {
                                    const isGranted = hasPermission(permission.key);
                                    return (
                                        <div
                                            key={permission.key}
                                            className={`px-4 py-3 flex items-center justify-between ${
                                                isGranted ? 'bg-white bg-opacity-60' : 'bg-white bg-opacity-30'
                                            }`}
                                        >
                                            <div className="flex-1">
                                                <div className="flex items-center">
                                                    <div className={`w-3 h-3 rounded-full mr-3 ${
                                                        isGranted ? 'bg-green-500' : 'bg-gray-300'
                                                    }`}></div>
                                                    <div>
                                                        <h5 className={`text-sm font-medium ${
                                                            isGranted ? 'text-gray-900' : 'text-gray-500'
                                                        }`}>
                                                            {permission.name}
                                                        </h5>
                                                        <p className={`text-xs ${
                                                            isGranted ? 'text-gray-600' : 'text-gray-400'
                                                        }`}>
                                                            {permission.description}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="ml-3">
                                                {isGranted ? (
                                                    <svg className="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                ) : (
                                                    <svg className="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    );
                })}
            </div>

            {/* Leyenda y notas */}
            <div className="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 className="text-sm font-medium text-gray-900 mb-3 flex items-center">
                    <svg className="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Información sobre Permisos
                </h4>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div>
                        <h5 className="font-medium text-gray-700 mb-1">Niveles de Acceso:</h5>
                        <ul className="space-y-1">
                            <li>• <span className="text-purple-600 font-medium">Máximo:</span> 90%+ permisos (Owner)</li>
                            <li>• <span className="text-blue-600 font-medium">Alto:</span> 70%+ permisos (Admin, Gerente)</li>
                            <li>• <span className="text-green-600 font-medium">Medio:</span> 50%+ permisos (Operativo)</li>
                            <li>• <span className="text-yellow-600 font-medium">Básico:</span> 30%+ permisos (Específico)</li>
                        </ul>
                    </div>
                    <div>
                        <h5 className="font-medium text-gray-700 mb-1">Notas Importantes:</h5>
                        <ul className="space-y-1">
                            <li>• Los permisos se asignan automáticamente por rol</li>
                            <li>• Algunos permisos requieren características Premium</li>
                            <li>• Los cambios se aplican en el próximo inicio de sesión</li>
                            <li>• Los Owners tienen todos los permisos por defecto</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default PermissionMatrix;
