import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    WrenchScrewdriverIcon, 
    ClockIcon,
    CheckCircleIcon 
} from '@heroicons/react/24/outline';

interface PlaceholderProps {
    module: string;
    description?: string;
    status?: 'development' | 'coming-soon' | 'completed';
}

export default function Placeholder({ 
    module, 
    description = 'Este módulo está en desarrollo', 
    status = 'development' 
}: PlaceholderProps) {
    
    const statusConfig = {
        development: {
            icon: WrenchScrewdriverIcon,
            color: 'text-yellow-600',
            bgColor: 'bg-yellow-50',
            borderColor: 'border-yellow-200',
            title: 'En Desarrollo',
            message: 'Estamos trabajando en este módulo.'
        },
        'coming-soon': {
            icon: ClockIcon,
            color: 'text-blue-600',
            bgColor: 'bg-blue-50',
            borderColor: 'border-blue-200',
            title: 'Próximamente',
            message: 'Este módulo estará disponible pronto.'
        },
        completed: {
            icon: CheckCircleIcon,
            color: 'text-green-600',
            bgColor: 'bg-green-50',
            borderColor: 'border-green-200',
            title: 'Completado',
            message: 'Este módulo está completamente funcional.'
        }
    };

    const config = statusConfig[status];
    const Icon = config.icon;

    return (
        <AuthenticatedLayout title={module}>
            <Head title={module} />
            
            <div className="max-w-4xl mx-auto">
                <div className={`rounded-lg p-8 text-center ${config.bgColor} ${config.borderColor} border-2 border-dashed`}>
                    <Icon className={`mx-auto h-16 w-16 ${config.color} mb-4`} />
                    
                    <h1 className={`text-2xl font-bold ${config.color} mb-2`}>
                        {module}
                    </h1>
                    
                    <div className="mb-4">
                        <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${config.bgColor} ${config.color} border ${config.borderColor}`}>
                            {config.title}
                        </span>
                    </div>
                    
                    <p className="text-gray-600 mb-6">
                        {description}
                    </p>
                    
                    <p className={`text-sm ${config.color} font-medium`}>
                        {config.message}
                    </p>
                    
                    <div className="mt-8 bg-white rounded-lg p-4 border border-gray-200">
                        <h3 className="text-sm font-medium text-gray-900 mb-3">
                            Funcionalidades Planificadas:
                        </h3>
                        <div className="text-left space-y-2">
                            <div className="flex items-center text-sm text-gray-600">
                                <div className="w-2 h-2 bg-gray-300 rounded-full mr-3"></div>
                                Interfaz moderna con React + TypeScript
                            </div>
                            <div className="flex items-center text-sm text-gray-600">
                                <div className="w-2 h-2 bg-gray-300 rounded-full mr-3"></div>
                                Integración completa con Inertia.js
                            </div>
                            <div className="flex items-center text-sm text-gray-600">
                                <div className="w-2 h-2 bg-gray-300 rounded-full mr-3"></div>
                                Validaciones y feedback en tiempo real
                            </div>
                            <div className="flex items-center text-sm text-gray-600">
                                <div className="w-2 h-2 bg-gray-300 rounded-full mr-3"></div>
                                Responsive y optimizado para móviles
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
