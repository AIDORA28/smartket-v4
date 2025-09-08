import React from 'react';
import { clsx } from 'clsx';
import { LockClosedIcon } from '@heroicons/react/24/outline';

export interface TabConfig {
  id: 'productos' | 'categorias' | 'marcas' | 'unidades';
  name: string;
  icon: React.ComponentType<any>;
  count: number;
  locked: boolean;
  emoji: string;
}

interface ProductTabsProps {
  tabs: TabConfig[];
  activeTab: string;
  onTabChange: (tabId: string, locked: boolean) => void;
  userRole: string;
}

export const ProductTabs: React.FC<ProductTabsProps> = ({
  tabs,
  activeTab,
  onTabChange,
  userRole
}) => {
  return (
    <div className="bg-white border-b border-gray-200 rounded-lg shadow-sm">
      <div className="flex space-x-0">
        {tabs.map((tab) => (
          <button
            key={tab.id}
            onClick={() => onTabChange(tab.id, tab.locked)}
            className={clsx(
              'relative px-6 py-4 text-sm font-medium border-b-2 transition-all duration-200 flex items-center gap-2',
              // Tab activo
              activeTab === tab.id && !tab.locked
                ? 'text-blue-600 border-blue-600 bg-blue-50'
                : '',
              // Tab disponible pero no activo
              activeTab !== tab.id && !tab.locked
                ? 'text-gray-600 border-transparent hover:text-gray-900 hover:border-gray-300 hover:bg-gray-50'
                : '',
              // Tab bloqueado
              tab.locked
                ? 'text-gray-400 border-transparent cursor-not-allowed bg-gray-100 opacity-60'
                : 'cursor-pointer',
              // Bordes
              'first:rounded-l-lg last:rounded-r-lg'
            )}
            disabled={tab.locked}
            title={tab.locked ? `Solo disponible para: Owner, Admin, Gerente (Tu rol: ${userRole})` : `Ver ${tab.name}`}
          >
            <tab.icon className={clsx('w-5 h-5', tab.locked && 'opacity-50')} />
            <span className="flex items-center gap-1">
              <span className="text-lg">{tab.emoji}</span>
              {tab.name}
            </span>
            <span className={clsx(
              'inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium',
              activeTab === tab.id && !tab.locked
                ? 'bg-blue-100 text-blue-700'
                : tab.locked
                ? 'bg-gray-200 text-gray-400'
                : 'bg-gray-100 text-gray-600'
            )}>
              {tab.count}
            </span>
            {/* Icono de candado para tabs bloqueados */}
            {tab.locked && (
              <LockClosedIcon className="w-4 h-4 text-gray-400 ml-1" />
            )}
          </button>
        ))}
      </div>
    </div>
  );
};

export default ProductTabs;
