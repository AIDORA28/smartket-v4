import React from 'react';

interface StatsCardProps {
    title: string;
    value: string;
    icon: React.ComponentType<{ className?: string }>;
    trend?: 'up' | 'down' | 'neutral';
    className?: string;
}

export default function StatsCard({ title, value, icon: Icon, trend = 'neutral', className = '' }: StatsCardProps) {
    const trendColors = {
        up: 'text-green-600',
        down: 'text-red-600',
        neutral: 'text-gray-600',
    };

    const borderColors = {
        up: 'border-green-200',
        down: 'border-red-200', 
        neutral: 'border-gray-200',
    };

    return (
        <div className={`bg-white overflow-hidden shadow rounded-lg border-l-4 ${borderColors[trend]} ${className}`}>
            <div className="p-5">
                <div className="flex items-center">
                    <div className="flex-shrink-0">
                        <Icon className={`h-6 w-6 ${trendColors[trend]}`} />
                    </div>
                    <div className="ml-5 w-0 flex-1">
                        <dl>
                            <dt className="text-sm font-medium text-gray-500 truncate">
                                {title}
                            </dt>
                            <dd className="text-lg font-medium text-gray-900">
                                {value}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    );
}
