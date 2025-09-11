import React from 'react';
import { Link, router } from '@inertiajs/react';
import { 
  PencilIcon, 
  TrashIcon, 
  UserCircleIcon,
  BuildingOfficeIcon,
  MapPinIcon,
  CheckCircleIcon,
  XCircleIcon
} from '@heroicons/react/24/outline';
import { UserListItem } from '@/Types/core';

const StatusBadge = ({ active }: { active: boolean }) => (
  <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
    active 
      ? 'bg-green-100 text-green-800'
      : 'bg-red-100 text-red-800'
  }`}>
    {active ? (
      <>
        <CheckCircleIcon className="w-3 h-3 mr-1" />
        Activo
      </>
    ) : (
      <>
        <XCircleIcon className="w-3 h-3 mr-1" />
        Inactivo
      </>
    )}
  </span>
);

const RoleBadge = ({ role }: { role: string }) => {
  const getRoleColor = (role: string) => {
    switch (role.toLowerCase()) {
      case 'owner':
        return 'bg-purple-100 text-purple-800';
      case 'admin':
        return 'bg-blue-100 text-blue-800';
      case 'vendedor':
        return 'bg-green-100 text-green-800';
      case 'cajero':
        return 'bg-yellow-100 text-yellow-800';
      case 'almacenero':
        return 'bg-orange-100 text-orange-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize ${getRoleColor(role)}`}>
      {role}
    </span>
  );
};

const UserInfo = ({ user }: { user: UserListItem }) => (
  <div className="flex items-center">
    <div className="flex-shrink-0 h-10 w-10">
      <div className="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
        <UserCircleIcon className="h-6 w-6 text-gray-500" />
      </div>
    </div>
    <div className="ml-4">
      <div className="text-sm font-medium text-gray-900">{user.name}</div>
      <div className="text-sm text-gray-500">{user.email}</div>
    </div>
  </div>
);

const CompanyInfo = ({ user }: { user: UserListItem }) => (
  <div className="text-sm text-gray-900">
    {user.empresa && (
      <div className="flex items-center mb-1">
        <BuildingOfficeIcon className="w-4 h-4 mr-1 text-gray-400" />
        {user.empresa.nombre}
      </div>
    )}
    {user.sucursal && (
      <div className="flex items-center text-gray-500">
        <MapPinIcon className="w-4 h-4 mr-1 text-gray-400" />
        {user.sucursal.nombre}
      </div>
    )}
    {!user.empresa && !user.sucursal && (
      <span className="text-gray-400">Sin asignar</span>
    )}
  </div>
);

const ActionButtons = ({ user }: { user: UserListItem }) => {
  const handleDelete = () => {
    if (confirm(`¿Estás seguro de que quieres eliminar al usuario "${user.name}"? Esta acción no se puede deshacer.`)) {
      router.delete(`/core/users/${user.id}`, {
        onSuccess: () => {
          // El redirect será manejado por el backend
        }
      });
    }
  };

  return (
    <div className="flex items-center space-x-2">
      <Link
        href={`/core/users/${user.id}/edit`}
        className="text-blue-600 hover:text-blue-800 transition-colors"
        title="Editar usuario"
      >
        <PencilIcon className="w-4 h-4" />
      </Link>
      <button
        onClick={handleDelete}
        className="text-red-600 hover:text-red-800 transition-colors"
        title="Eliminar usuario"
      >
        <TrashIcon className="w-4 h-4" />
      </button>
    </div>
  );
};

interface UserTableProps {
  users: UserListItem[];
  onDelete?: (userId: number) => void;
}

export default function UserTable({ users, onDelete }: UserTableProps) {
  if (users.length === 0) {
    return (
      <div className="bg-white shadow rounded-lg">
        <div className="text-center py-12">
          <UserCircleIcon className="mx-auto h-12 w-12 text-gray-400" />
          <h3 className="mt-2 text-sm font-medium text-gray-900">No hay usuarios</h3>
          <p className="mt-1 text-sm text-gray-500">
            Comienza creando un nuevo usuario.
          </p>
          <div className="mt-6">
            <Link
              href="/core/users/create"
              className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              Crear Usuario
            </Link>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="bg-white shadow overflow-hidden sm:rounded-lg">
      <div className="overflow-x-auto">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Usuario
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Rol
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Empresa/Sucursal
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Estado
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Fecha Registro
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {users.map((user) => (
              <tr key={user.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 whitespace-nowrap">
                  <UserInfo user={user} />
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <RoleBadge role={user.rol_principal} />
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <CompanyInfo user={user} />
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <StatusBadge active={user.activo} />
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {new Date(user.created_at).toLocaleDateString('es-ES')}
                  {user.last_login && (
                    <div className="text-xs text-gray-400">
                      Último acceso: {new Date(user.last_login).toLocaleDateString('es-ES')}
                    </div>
                  )}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <ActionButtons user={user} />
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
