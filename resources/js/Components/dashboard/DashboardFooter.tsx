import React from 'react';

export default function DashboardFooter() {
  return (
    <div className="text-center py-8">
      <div className="inline-flex items-center space-x-6 bg-white rounded-2xl px-8 py-4 shadow-xl border border-gray-100">
        <div className="flex items-center space-x-2">
          <div className="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
          <span className="text-sm font-semibold text-gray-700">SmartKet ERP v4.0</span>
        </div>
        <div className="w-px h-6 bg-gray-300"></div>
        <span className="text-xs text-gray-500">💪 Sistema optimizado</span>
        <div className="w-px h-6 bg-gray-300"></div>
        <span className="text-xs text-gray-500">🛡️ 100% seguro</span>
      </div>
    </div>
  );
}
