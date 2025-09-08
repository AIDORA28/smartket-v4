import React from 'react';

interface AnimatedIconProps {
  type: 'productos' | 'categorias' | 'marcas' | 'unidades';
  className?: string;
}

export const AnimatedIcon: React.FC<AnimatedIconProps> = ({ type, className = "w-24 h-24" }) => {
  const icons = {
    productos: {
      emoji: '📦',
      color: '#3B82F6',
      bgColor: '#DBEAFE',
      animation: 'animate-bounce'
    },
    categorias: {
      emoji: '🏷️',
      color: '#8B5CF6',
      bgColor: '#EDE9FE',
      animation: 'animate-pulse'
    },
    marcas: {
      emoji: '🏪',
      color: '#F59E0B',
      bgColor: '#FEF3C7',
      animation: 'animate-spin'
    },
    unidades: {
      emoji: '⚖️',
      color: '#10B981',
      bgColor: '#D1FAE5',
      animation: 'animate-pulse'
    }
  };

  const icon = icons[type];

  return (
    <div className={`${className} mx-auto relative`}>
      <svg
        className="w-full h-full"
        viewBox="0 0 100 100"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        {/* Círculo de fondo */}
        <circle 
          cx="50" 
          cy="50" 
          r="45" 
          fill={icon.bgColor} 
          stroke={icon.color} 
          strokeWidth="2"
          className="animate-pulse"
        />
        
        {/* Círculo central animado */}
        <circle 
          cx="50" 
          cy="50" 
          r="35" 
          fill={icon.color} 
          className={icon.animation}
          style={{
            transformOrigin: '50% 50%',
            animationDuration: type === 'marcas' ? '3s' : '2s'
          }}
        />
        
        {/* Texto del emoji */}
        <text 
          x="50" 
          y="58" 
          textAnchor="middle" 
          fontSize="20" 
          fill="white"
          className="select-none"
        >
          {icon.emoji}
        </text>
        
        {/* Efectos de partículas para productos */}
        {type === 'productos' && (
          <>
            <circle cx="25" cy="25" r="2" fill={icon.color} className="animate-ping" />
            <circle cx="75" cy="25" r="2" fill={icon.color} className="animate-ping" style={{ animationDelay: '0.5s' }} />
            <circle cx="25" cy="75" r="2" fill={icon.color} className="animate-ping" style={{ animationDelay: '1s' }} />
            <circle cx="75" cy="75" r="2" fill={icon.color} className="animate-ping" style={{ animationDelay: '1.5s' }} />
          </>
        )}
        
        {/* Líneas rotativas para marcas */}
        {type === 'marcas' && (
          <g className="animate-spin" style={{ transformOrigin: '50px 50px', animationDuration: '4s' }}>
            <line x1="20" y1="50" x2="80" y2="50" stroke={icon.color} strokeWidth="2" opacity="0.3" />
            <line x1="50" y1="20" x2="50" y2="80" stroke={icon.color} strokeWidth="2" opacity="0.3" />
            <line x1="29" y1="29" x2="71" y2="71" stroke={icon.color} strokeWidth="2" opacity="0.3" />
            <line x1="71" y1="29" x2="29" y2="71" stroke={icon.color} strokeWidth="2" opacity="0.3" />
          </g>
        )}
        
        {/* Ondas para unidades */}
        {type === 'unidades' && (
          <>
            <circle cx="50" cy="50" r="25" fill="none" stroke={icon.color} strokeWidth="1" opacity="0.4" className="animate-ping" />
            <circle cx="50" cy="50" r="30" fill="none" stroke={icon.color} strokeWidth="1" opacity="0.3" className="animate-ping" style={{ animationDelay: '0.5s' }} />
            <circle cx="50" cy="50" r="35" fill="none" stroke={icon.color} strokeWidth="1" opacity="0.2" className="animate-ping" style={{ animationDelay: '1s' }} />
          </>
        )}
        
        {/* Destellos para categorías */}
        {type === 'categorias' && (
          <>
            <path d="M50 15 L52 20 L57 20 L53 24 L55 29 L50 26 L45 29 L47 24 L43 20 L48 20 Z" fill="white" opacity="0.8" className="animate-pulse" />
            <path d="M15 50 L17 55 L22 55 L18 59 L20 64 L15 61 L10 64 L12 59 L8 55 L13 55 Z" fill="white" opacity="0.6" className="animate-pulse" style={{ animationDelay: '0.3s' }} />
            <path d="M85 50 L87 55 L92 55 L88 59 L90 64 L85 61 L80 64 L82 59 L78 55 L83 55 Z" fill="white" opacity="0.6" className="animate-pulse" style={{ animationDelay: '0.6s' }} />
          </>
        )}
      </svg>
      
      {/* Tooltip informativo */}
      <div className="absolute -bottom-8 left-1/2 transform -translate-x-1/2 text-xs text-gray-500 text-center whitespace-nowrap">
        Gestión de {type}
      </div>
    </div>
  );
};

export default AnimatedIcon;
