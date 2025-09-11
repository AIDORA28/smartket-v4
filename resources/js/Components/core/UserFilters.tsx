import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import { 
  MagnifyingGlassIcon, 
  FunnelIcon,
  XMarkIcon 
} from '@heroicons/react/24/outline';
import { Role, Empresa } from '@/Types/core';

const SearchInput = ({ 
  value, 
  onChange 
}: { 
  value: string; 
  onChange: (value: string) => void; 
}) => (
  <div className="relative flex-1 min-w-0">
    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <MagnifyingGlassIcon className="h-5 w-5 text-gray-400" />
    </div>
    <input
      type="text"
      placeholder="Buscar usuarios por nombre o email..."
      value={value}
      onChange={(e) => onChange(e.target.value)}
      className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-red-500 focus:border-red-500 sm:text-sm"
    />
  </div>
);

const RoleFilter = ({ 
  value, 
  roles, 
  onChange 
}: { 
  value: string; 
  roles: Role[]; 
  onChange: (role: string) => void; 
}) => (
  <select
    value={value}
    onChange={(e) => onChange(e.target.value)}
    className="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md"
  >
    <option value="">Todos los roles</option>
    {roles.map((role) => (
      <option key={role.key} value={role.key}>
        {role.display_name}
      </option>
    ))}
  </select>
);

const CompanyFilter = ({ 
  value, 
  empresas, 
  onChange 
}: { 
  value: string; 
  empresas: Empresa[]; 
  onChange: (empresa: string) => void; 
}) => (
  <select
    value={value}
    onChange={(e) => onChange(e.target.value)}
    className="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md"
  >
    <option value="">Todas las empresas</option>
    {empresas.map((empresa) => (
      <option key={empresa.id} value={empresa.id.toString()}>
        {empresa.nombre}
      </option>
    ))}
  </select>
);

const StatusFilter = ({ 
  value, 
  onChange 
}: { 
  value: string; 
  onChange: (status: string) => void; 
}) => (
  <select
    value={value}
    onChange={(e) => onChange(e.target.value)}
    className="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md"
  >
    <option value="">Todos los estados</option>
    <option value="active">Activos</option>
    <option value="inactive">Inactivos</option>
  </select>
);

const ActiveFilters = ({ 
  filters, 
  roles, 
  empresas, 
  onClear 
}: {
  filters: any;
  roles: Role[];
  empresas: Empresa[];
  onClear: (key: 'search' | 'role' | 'empresa' | 'status') => void;
}) => {
  const activeFilters: Array<{ key: 'search' | 'role' | 'empresa' | 'status'; label: string }> = [];
  
  if (filters.search) {
    activeFilters.push({ key: 'search', label: `Búsqueda: "${filters.search}"` });
  }
  
  if (filters.role) {
    const role = roles.find(r => r.key === filters.role);
    activeFilters.push({ key: 'role', label: `Rol: ${role?.display_name}` });
  }
  
  if (filters.empresa) {
    const empresa = empresas.find(e => e.id.toString() === filters.empresa);
    activeFilters.push({ key: 'empresa', label: `Empresa: ${empresa?.nombre}` });
  }
  
  if (filters.status) {
    const statusLabel = filters.status === 'active' ? 'Activos' : 'Inactivos';
    activeFilters.push({ key: 'status', label: `Estado: ${statusLabel}` });
  }

  if (activeFilters.length === 0) return null;

  return (
    <div className="flex flex-wrap gap-2 mt-3">
      <span className="text-sm text-gray-500">Filtros activos:</span>
      {activeFilters.map((filter) => (
        <span
          key={filter.key}
          className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
        >
          {filter.label}
          <button
            type="button"
            className="ml-1 inline-flex items-center justify-center h-4 w-4 rounded-full text-red-400 hover:bg-red-200 hover:text-red-500 focus:outline-none focus:bg-red-500 focus:text-white"
            onClick={() => onClear(filter.key)}
          >
            <XMarkIcon className="h-3 w-3" />
          </button>
        </span>
      ))}
    </div>
  );
};

interface UserFiltersProps {
  filters: {
    search?: string;
    role?: string;
    empresa?: string;
    status?: string;
  };
  roles: Role[];
  empresas: Empresa[];
}

export default function UserFilters({ filters, roles, empresas }: UserFiltersProps) {
  const [localFilters, setLocalFilters] = useState(filters);

  const updateFilter = (key: keyof typeof localFilters, value: string) => {
    const newFilters = { ...localFilters, [key]: value };
    if (!value) delete newFilters[key];
    
    setLocalFilters(newFilters);
    
    // Debounced search
    if (key === 'search') {
      const timer = setTimeout(() => {
        router.get('/core/users', newFilters, { preserveState: true });
      }, 300);
      return () => clearTimeout(timer);
    } else {
      router.get('/core/users', newFilters, { preserveState: true });
    }
  };

  const clearFilter = (key: keyof typeof localFilters) => {
    updateFilter(key, '');
  };

  const clearAllFilters = () => {
    setLocalFilters({});
    router.get('/core/users', {}, { preserveState: true });
  };

  return (
    <div className="bg-white p-4 rounded-lg shadow">
      <div className="flex items-center space-x-4">
        <div className="flex items-center text-gray-500">
          <FunnelIcon className="h-5 w-5 mr-2" />
          <span className="text-sm font-medium">Filtros</span>
        </div>
        
        <SearchInput
          value={localFilters.search || ''}
          onChange={(value) => updateFilter('search', value)}
        />
        
        <div className="flex space-x-3">
          <div className="w-40">
            <RoleFilter
              value={localFilters.role || ''}
              roles={roles}
              onChange={(role) => updateFilter('role', role)}
            />
          </div>
          
          <div className="w-48">
            <CompanyFilter
              value={localFilters.empresa || ''}
              empresas={empresas}
              onChange={(empresa) => updateFilter('empresa', empresa)}
            />
          </div>
          
          <div className="w-32">
            <StatusFilter
              value={localFilters.status || ''}
              onChange={(status) => updateFilter('status', status)}
            />
          </div>
        </div>
        
        {(localFilters.search || localFilters.role || localFilters.empresa || localFilters.status) && (
          <button
            onClick={clearAllFilters}
            className="text-sm text-gray-500 hover:text-gray-700 underline"
          >
            Limpiar filtros
          </button>
        )}
      </div>
      
      <ActiveFilters
        filters={localFilters}
        roles={roles}
        empresas={empresas}
        onClear={clearFilter}
      />
    </div>
  );
}
