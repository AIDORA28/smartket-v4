import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import UserForm from '@/Components/core/UserForm';
import { Role, Empresa, Sucursal, UserFormData } from '@/Types/core';

interface EditUserPageProps {
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
            rol_principal: string;
        };
    };
    user: UserFormData;
    roles: Role[];
    empresas: Empresa[];
    sucursales: Sucursal[];
}

const Edit: React.FC<EditUserPageProps> = ({ auth, user, roles, empresas, sucursales }) => {
    const handleCancel = () => {
        window.history.back();
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Editar Usuario - ${user.name}`} />
            
            <div className="py-6">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    {/* Breadcrumb */}
                    <nav className="flex mb-6" aria-label="Breadcrumb">
                        <ol className="inline-flex items-center space-x-1 md:space-x-3">
                            <li className="inline-flex items-center">
                                <a
                                    href="/dashboard"
                                    className="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600"
                                >
                                    <svg className="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                    </svg>
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <div className="flex items-center">
                                    <svg className="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clipRule="evenodd" />
                                    </svg>
                                    <a
                                        href="/core/users"
                                        className="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2"
                                    >
                                        Usuarios
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div className="flex items-center">
                                    <svg className="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clipRule="evenodd" />
                                    </svg>
                                    <span className="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                                        Editar Usuario
                                    </span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {/* Header */}
                    <div className="mb-8">
                        <div className="flex items-center justify-between">
                            <div>
                                <h1 className="text-2xl font-bold text-gray-900 flex items-center">
                                    <div className="p-2 bg-blue-100 rounded-lg mr-3">
                                        <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                    Editar Usuario
                                </h1>
                                <p className="mt-2 text-gray-600">
                                    Modifique la información del usuario <span className="font-semibold text-gray-900">{user.name}</span>
                                </p>
                            </div>
                            
                            <div className="flex items-center space-x-3">
                                <button
                                    onClick={handleCancel}
                                    className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                >
                                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    Volver
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Información del usuario actual */}
                    <div className="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                            <div className="ml-4 flex-1">
                                <h3 className="text-lg font-medium text-gray-900">
                                    {user.name}
                                </h3>
                                <div className="mt-1 flex items-center space-x-4">
                                    <span className="text-sm text-gray-600">
                                        <strong>Email:</strong> {user.email}
                                    </span>
                                    <span className="text-sm text-gray-600">
                                        <strong>Rol:</strong> 
                                        <span className={`ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                            user.rol_principal === 'owner' ? 'bg-purple-100 text-purple-800' :
                                            user.rol_principal === 'admin' ? 'bg-blue-100 text-blue-800' :
                                            user.rol_principal === 'gerente' ? 'bg-indigo-100 text-indigo-800' :
                                            user.rol_principal === 'vendedor' ? 'bg-green-100 text-green-800' :
                                            'bg-gray-100 text-gray-800'
                                        }`}>
                                            {user.rol_principal}
                                        </span>
                                    </span>
                                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                        user.activo 
                                            ? 'bg-green-100 text-green-800' 
                                            : 'bg-red-100 text-red-800'
                                    }`}>
                                        {user.activo ? 'Activo' : 'Inactivo'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Advertencias importantes */}
                    <div className="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div className="flex">
                            <div className="flex-shrink-0">
                                <svg className="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div className="ml-3">
                                <h3 className="text-sm font-medium text-yellow-800">
                                    Precauciones al editar usuario
                                </h3>
                                <div className="mt-2 text-sm text-yellow-700">
                                    <ul className="list-disc pl-5 space-y-1">
                                        <li>Cambiar el rol afectará los permisos y accesos del usuario</li>
                                        <li>Cambiar la empresa/sucursal puede afectar el acceso a datos</li>
                                        <li>Desactivar un usuario impedirá su acceso al sistema</li>
                                        <li>Los cambios se aplicarán inmediatamente</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Actividad reciente (simulada) */}
                    <div className="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                        <h4 className="text-sm font-medium text-gray-900 mb-3 flex items-center">
                            <svg className="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Actividad Reciente
                        </h4>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div className="text-center p-3 bg-gray-50 rounded-lg">
                                <div className="text-lg font-semibold text-gray-900">--</div>
                                <div className="text-sm text-gray-500">Último acceso</div>
                            </div>
                            <div className="text-center p-3 bg-gray-50 rounded-lg">
                                <div className="text-lg font-semibold text-gray-900">--</div>
                                <div className="text-sm text-gray-500">Sesiones activas</div>
                            </div>
                            <div className="text-center p-3 bg-gray-50 rounded-lg">
                                <div className="text-lg font-semibold text-gray-900">--</div>
                                <div className="text-sm text-gray-500">Acciones realizadas</div>
                            </div>
                        </div>
                    </div>

                    {/* Formulario de Edición */}
                    <UserForm
                        user={user}
                        roles={roles}
                        empresas={empresas}
                        sucursales={sucursales}
                        mode="edit"
                        onCancel={handleCancel}
                    />

                    {/* Sección de acciones adicionales */}
                    <div className="mt-6 bg-gray-50 rounded-lg p-4">
                        <h4 className="text-sm font-medium text-gray-900 mb-3">Acciones Adicionales</h4>
                        <div className="flex flex-wrap gap-2">
                            {user.activo ? (
                                <button className="inline-flex items-center px-3 py-1 border border-red-300 shadow-sm text-xs font-medium rounded text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg className="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                    </svg>
                                    Desactivar Usuario
                                </button>
                            ) : (
                                <button className="inline-flex items-center px-3 py-1 border border-green-300 shadow-sm text-xs font-medium rounded text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg className="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Activar Usuario
                                </button>
                            )}
                            <button className="inline-flex items-center px-3 py-1 border border-blue-300 shadow-sm text-xs font-medium rounded text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg className="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 7a2 2 0 012 2m0 0v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2m6 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v2m6 0H9" />
                                </svg>
                                Resetear Contraseña
                            </button>
                            <button className="inline-flex items-center px-3 py-1 border border-yellow-300 shadow-sm text-xs font-medium rounded text-yellow-700 bg-white hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <svg className="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Ver Historial de Actividad
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default Edit;
