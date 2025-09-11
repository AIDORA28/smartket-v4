import React from 'react';
import { BuildingOfficeIcon, MapPinIcon, StarIcon } from '@heroicons/react/24/outline';
import { ContextIndicatorProps } from '@/Types/core';

const CompanyInfo = ({ empresa }: { empresa: any }) => (
  <div className="flex items-center space-x-2">
    <div className="flex items-center justify-center w-8 h-8 bg-red-100 rounded-lg">
      <BuildingOfficeIcon className="w-4 h-4 text-red-600" />
    </div>
    <div>
      <div className="text-sm font-medium text-gray-900">{empresa.nombre}</div>
      {empresa.plan && (
        <div className="flex items-center text-xs text-gray-500">
          <StarIcon className="w-3 h-3 mr-1" />
          Plan {empresa.plan.nombre}
        </div>
      )}
    </div>
  </div>
);

const BranchInfo = ({ sucursal }: { sucursal: any }) => (
  <div className="flex items-center space-x-2">
    <div className="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-lg">
      <MapPinIcon className="w-4 h-4 text-blue-600" />
    </div>
    <div>
      <div className="text-sm font-medium text-gray-900">{sucursal.nombre}</div>
      {sucursal.direccion && (
        <div className="text-xs text-gray-500">{sucursal.direccion}</div>
      )}
    </div>
  </div>
);

const EmptyState = ({ type }: { type: 'empresa' | 'sucursal' }) => (
  <div className="flex items-center space-x-2">
    <div className={`flex items-center justify-center w-8 h-8 ${
      type === 'empresa' ? 'bg-gray-100' : 'bg-gray-100'
    } rounded-lg`}>
      {type === 'empresa' ? (
        <BuildingOfficeIcon className="w-4 h-4 text-gray-400" />
      ) : (
        <MapPinIcon className="w-4 h-4 text-gray-400" />
      )}
    </div>
    <div>
      <div className="text-sm font-medium text-gray-400">
        Sin {type === 'empresa' ? 'empresa' : 'sucursal'}
      </div>
      <div className="text-xs text-gray-400">
        {type === 'empresa' ? 'Selecciona una empresa' : 'Selecciona una sucursal'}
      </div>
    </div>
  </div>
);

export default function ContextIndicator({ 
  empresa, 
  sucursal, 
  planInfo 
}: ContextIndicatorProps) {
  return (
    <div className="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
      <div className="flex items-center justify-between mb-3">
        <h3 className="text-sm font-medium text-gray-700">Contexto Actual</h3>
        <div className="flex items-center space-x-1">
          <div className="w-2 h-2 bg-green-500 rounded-full"></div>
          <span className="text-xs text-green-600 font-medium">Activo</span>
        </div>
      </div>
      
      <div className="space-y-3">
        {/* Empresa */}
        {empresa ? (
          <CompanyInfo empresa={empresa} />
        ) : (
          <EmptyState type="empresa" />
        )}
        
        {/* Separador */}
        <div className="border-t border-gray-100"></div>
        
        {/* Sucursal */}
        {sucursal ? (
          <BranchInfo sucursal={sucursal} />
        ) : (
          <EmptyState type="sucursal" />
        )}
      </div>
      
      {/* Footer con información adicional */}
      {planInfo && (
        <div className="mt-3 pt-3 border-t border-gray-100">
          <div className="text-xs text-gray-500">{planInfo}</div>
        </div>
      )}
    </div>
  );
}
