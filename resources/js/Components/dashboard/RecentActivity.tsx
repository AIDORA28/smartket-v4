import React from 'react';
import { Card, CardHeader, CardBody } from '../ui/Card';
import {
  ClockIcon,
  SparklesIcon
} from '@heroicons/react/24/outline';

interface Activity {
  type: string;
  message: string;
  time: string;
  icon: string;
  color: string;
}

interface RecentActivityProps {
  recentActivity: Activity[];
}

export default function RecentActivity({ recentActivity }: RecentActivityProps) {
  const getColorClasses = (color: string) => {
    const colors = {
      blue: 'bg-blue-50 text-blue-700 border-blue-200',
      green: 'bg-green-50 text-green-700 border-green-200',
      purple: 'bg-purple-50 text-purple-700 border-purple-200',
      amber: 'bg-amber-50 text-amber-700 border-amber-200',
      red: 'bg-red-50 text-red-700 border-red-200',
    };
    return colors[color as keyof typeof colors] || colors.blue;
  };

  return (
    <Card>
      <CardHeader>
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 bg-gradient-to-br from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
            <ClockIcon className="w-6 h-6 text-white" />
          </div>
          <div>
            <h3 className="text-lg font-semibold text-gray-900">Actividad Reciente</h3>
            <p className="text-sm text-gray-500">Últimos cambios en el sistema</p>
          </div>
        </div>
      </CardHeader>
      <CardBody>
        {recentActivity && recentActivity.length > 0 ? (
          <div className="space-y-4">
            {recentActivity.map((activity, index) => (
              <div key={index} className="flex items-start gap-3">
                <div className={`
                  w-8 h-8 rounded-full flex items-center justify-center border
                  ${getColorClasses(activity.color)}
                `}>
                  <span className="text-sm">{activity.icon}</span>
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium text-gray-900">{activity.message}</p>
                  <p className="text-xs text-gray-500 mt-1">{activity.time}</p>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="text-center py-8">
            <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <SparklesIcon className="w-8 h-8 text-gray-400" />
            </div>
            <h3 className="text-sm font-medium text-gray-900 mb-2">No hay actividad reciente</h3>
            <p className="text-xs text-gray-500">
              La actividad del sistema aparecerá aquí cuando realices cambios.
            </p>
          </div>
        )}
      </CardBody>
    </Card>
  );
}
