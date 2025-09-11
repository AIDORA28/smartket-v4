import React from 'react';
import { Link } from '@inertiajs/react';

interface ActionCardProps {
  title: string;
  description: string;
  emoji: string;
  href: string;
  color: 'green' | 'blue' | 'purple' | 'orange' | 'red' | 'indigo';
  disabled?: boolean;
  onClick?: () => void;
}

const ActionCard: React.FC<ActionCardProps> = ({
  title,
  description,
  emoji,
  href,
  color,
  disabled = false,
  onClick
}) => {
  const getColorClasses = (color: ActionCardProps['color']) => {
    const colors = {
      green: 'bg-green-50 hover:bg-green-100 border-green-200 text-green-700',
      blue: 'bg-blue-50 hover:bg-blue-100 border-blue-200 text-blue-700',
      purple: 'bg-purple-50 hover:bg-purple-100 border-purple-200 text-purple-700',
      orange: 'bg-orange-50 hover:bg-orange-100 border-orange-200 text-orange-700',
      red: 'bg-red-50 hover:bg-red-100 border-red-200 text-red-700',
      indigo: 'bg-indigo-50 hover:bg-indigo-100 border-indigo-200 text-indigo-700'
    };
    return colors[color];
  };

  const getIconBg = (color: ActionCardProps['color']) => {
    const colors = {
      green: 'bg-green-600',
      blue: 'bg-blue-600',
      purple: 'bg-purple-600',
      orange: 'bg-orange-600',
      red: 'bg-red-600',
      indigo: 'bg-indigo-600'
    };
    return colors[color];
  };

  const handleClick = (e: React.MouseEvent) => {
    if (disabled) {
      e.preventDefault();
      return;
    }
    if (onClick) {
      onClick();
    }
  };

  const className = `
    flex items-center p-4 rounded-xl border transition-colors group
    ${disabled 
      ? 'bg-gray-50 border-gray-200 text-gray-400 cursor-not-allowed opacity-60' 
      : getColorClasses(color)
    }
  `;

  return (
    <Link
      href={disabled ? '#' : href}
      onClick={handleClick}
      className={className}
    >
      <div className={`w-10 h-10 rounded-lg flex items-center justify-center mr-3 ${
        disabled ? 'bg-gray-400' : getIconBg(color)
      }`}>
        <span className="text-white text-xl">{emoji}</span>
      </div>
      <div className="flex-1">
        <p className={`font-medium ${disabled ? 'text-gray-500' : 'text-gray-900 group-hover:' + color + '-700'}`}>
          {title}
        </p>
        <p className="text-sm text-gray-600">{description}</p>
      </div>
      {!disabled && (
        <span className="text-gray-400 group-hover:text-gray-600">→</span>
      )}
    </Link>
  );
};

export default ActionCard;
