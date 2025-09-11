import React from 'react';
import { Link } from '@inertiajs/react';
import {
  ChartBarIcon,
  CurrencyDollarIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon
} from '@heroicons/react/24/outline';

interface MetricCardProps {
  title: string;
  value: string | number;
  emoji: string;
  trend?: {
    value: number;
    isPositive: boolean;
  };
  color: 'green' | 'blue' | 'purple' | 'orange' | 'red' | 'indigo';
  subtitle?: string;
  href?: string;
}

const MetricCard: React.FC<MetricCardProps> = ({
  title,
  value,
  emoji,
  trend,
  color,
  subtitle,
  href
}) => {
  const getColorClasses = (color: MetricCardProps['color']) => {
    const colors = {
      green: 'bg-green-100 text-green-600',
      blue: 'bg-blue-100 text-blue-600', 
      purple: 'bg-purple-100 text-purple-600',
      orange: 'bg-orange-100 text-orange-600',
      red: 'bg-red-100 text-red-600',
      indigo: 'bg-indigo-100 text-indigo-600'
    };
    return colors[color];
  };

  return (
    <div className={`bg-white rounded-xl shadow-sm border border-gray-200 p-6 ${
      href ? 'hover:shadow-md transition-shadow' : ''
    }`}>
      {href ? (
        <Link href={href} className="block">
          <CardContent />
        </Link>
      ) : (
        <CardContent />
      )}
    </div>
  );

  function CardContent() {
    return (
      <div className="flex items-center">
        <div className="flex-shrink-0">
          <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${getColorClasses(color)}`}>
            <span className="text-xl">{emoji}</span>
          </div>
        </div>
        <div className="ml-4 flex-1">
          <p className="text-sm font-medium text-gray-500">{title}</p>
          <p className="text-2xl font-bold text-gray-900">{value}</p>
          {trend && (
            <div className="flex items-center mt-1">
              {trend.isPositive ? (
                <ArrowTrendingUpIcon className="w-4 h-4 text-green-500" />
              ) : (
                <ArrowTrendingDownIcon className="w-4 h-4 text-red-500" />
              )}
              <span className={`text-sm ml-1 ${trend.isPositive ? 'text-green-600' : 'text-red-600'}`}>
                {Math.abs(trend.value)}%
              </span>
            </div>
          )}
          {subtitle && (
            <p className="text-sm text-gray-600 mt-1">{subtitle}</p>
          )}
        </div>
      </div>
    );
  }
};

export default MetricCard;
