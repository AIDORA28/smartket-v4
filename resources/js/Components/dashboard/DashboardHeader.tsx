import React from 'react';

interface DashboardHeaderProps {
  userName: string;
  empresaNombre: string;
  sucursalNombre: string;
  cajaStatus: {
    activa: boolean;
    caja_nombre?: string;
    codigo?: string;
    monto_inicial?: number;
    ventas_efectivo_hoy?: number;
    total_estimado?: number;
    mensaje?: string;
  };
}

export default function DashboardHeader({ 
  userName, 
  empresaNombre, 
  sucursalNombre, 
  cajaStatus 
}: DashboardHeaderProps) {
  const currentTime = new Date().toLocaleString('es-ES', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });

  return (
    <div className="bg-gradient-to-r from-red-600 via-red-700 to-red-800 rounded-2xl p-6 text-white shadow-xl">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold mb-2">
            ¡Hola, {userName.split(' ')[0]}! 👋
          </h1>
          <p className="text-red-100 text-lg mb-1">
            Todo marcha bien en {empresaNombre}
          </p>
          <p className="text-red-200 text-sm">
            {currentTime} • {sucursalNombre}
          </p>
        </div>
        
        {/* Estado de la caja */}
        <div className="text-right">
          {cajaStatus.activa ? (
            <div className="bg-white/20 backdrop-blur-sm rounded-xl p-4">
              <div className="flex items-center space-x-2 mb-2">
                <div className="w-3 h-3 bg-amber-400 rounded-full animate-pulse"></div>
                <span className="font-semibold">Caja Activa</span>
              </div>
              <p className="text-sm text-red-200">{cajaStatus.caja_nombre}</p>
              <p className="text-xs text-red-300">
                Efectivo estimado: S/ {cajaStatus.total_estimado?.toFixed(2) || '0.00'}
              </p>
            </div>
          ) : (
            <div className="bg-amber-500/20 backdrop-blur-sm rounded-xl p-4">
              <div className="flex items-center space-x-2 mb-2">
                <div className="w-3 h-3 bg-amber-400 rounded-full"></div>
                <span className="font-semibold">Caja Cerrada</span>
              </div>
              <p className="text-xs text-amber-200">{cajaStatus.mensaje}</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
