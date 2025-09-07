import React from 'react';
import { clsx } from 'clsx';
import { 
  ShoppingCartIcon, 
  ExclamationTriangleIcon, 
  UserGroupIcon,
  BanknotesIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  ArchiveBoxIcon,
  ChartBarIcon
} from '@heroicons/react/24/outline';

interface StatsCardProps {
  title: string;
  value: string | number;
  icon: 'cart' | 'warning' | 'users' | 'money' | 'up' | 'down' | 'products' | 'chart' | string;
  color: 'blue' | 'red' | 'green' | 'purple' | 'yellow' | 'gray' | 'orange';
  trend?: {
    value: number;
    label: string;
    direction: 'up' | 'down';
  };
  subtitle?: string;
  className?: string;
}

const iconMap = {
  cart: ShoppingCartIcon,
  warning: ExclamationTriangleIcon,
  users: UserGroupIcon,
  money: BanknotesIcon,
  up: ArrowTrendingUpIcon,
  down: ArrowTrendingDownIcon,
  products: ArchiveBoxIcon,
  chart: ChartBarIcon
};

const colorMap = {
  blue: {
    bg: 'bg-blue-50',
    icon: 'text-blue-600',
    text: 'text-blue-900'
  },
  red: {
    bg: 'bg-red-50',
    icon: 'text-red-600',
    text: 'text-red-900'
  },
  green: {
    bg: 'bg-green-50',
    icon: 'text-green-600',
    text: 'text-green-900'
  },
  purple: {
    bg: 'bg-purple-50',
    icon: 'text-purple-600',
    text: 'text-purple-900'
  },
  yellow: {
    bg: 'bg-yellow-50',
    icon: 'text-yellow-600',
    text: 'text-yellow-900'
  },
  gray: {
    bg: 'bg-gray-50',
    icon: 'text-gray-600',
    text: 'text-gray-900'
  },
  orange: {
    bg: 'bg-orange-50',
    icon: 'text-orange-600',
    text: 'text-orange-900'
  }
};

export function StatsCard({ title, value, icon, color, trend, subtitle, className }: StatsCardProps) {
  const IconComponent = iconMap[icon as keyof typeof iconMap];
  const colors = colorMap[color];

  return (
    <div className={clsx(
      'bg-white overflow-hidden shadow-lg rounded-xl border border-gray-200 hover:shadow-xl transition-shadow duration-300',
      className
    )}>
      <div className="p-6">
        <div className="flex items-center">
          <div className={clsx('flex-shrink-0 p-3 rounded-xl shadow-sm', colors.bg)}>
            {IconComponent ? (
              <IconComponent className={clsx('h-7 w-7', colors.icon)} />
            ) : (
              <span className="text-2xl">{icon}</span>
            )}
          </div>
          <div className="ml-5 flex-1">
            <div className="flex items-baseline justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600 truncate">
                  {title}
                </p>
                <p className={clsx('text-3xl font-bold mt-1', colors.text)}>
                  {value}
                </p>
                {subtitle && (
                  <p className="text-xs text-gray-500 mt-1">{subtitle}</p>
                )}
              </div>
              {trend && (
                <div className={clsx(
                  'inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold',
                  trend.direction === 'up' 
                    ? 'bg-green-100 text-green-800 shadow-sm' 
                    : 'bg-red-100 text-red-800 shadow-sm'
                )}>
                  {trend.direction === 'up' ? (
                    <ArrowTrendingUpIcon className="h-4 w-4 mr-1" />
                  ) : (
                    <ArrowTrendingDownIcon className="h-4 w-4 mr-1" />
                  )}
                  {trend.value}%
                </div>
              )}
            </div>
            {trend && (
              <p className="mt-2 text-sm text-gray-600 font-medium">
                {trend.label}
              </p>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
