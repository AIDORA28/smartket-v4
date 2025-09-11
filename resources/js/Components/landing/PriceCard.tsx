import React from 'react';
import { Link } from '@inertiajs/react';
import { Plan } from '@/Types/landing';

interface PriceCardProps {
  plan: Plan;
  billingCycle: 'mensual' | 'anual';
}

const PlanHeader = ({ plan, monthlyPrice, billingCycle }: { 
  plan: Plan; 
  monthlyPrice: number; 
  billingCycle: 'mensual' | 'anual' 
}) => (
  <div className="text-center mb-8 flex-shrink-0">
    <h3 className="text-2xl font-bold text-gray-900 mb-2">
      {plan.nombre}
    </h3>
    <p className="text-gray-600 mb-4">
      {plan.descripcion}
    </p>
    
    <div className="mb-4">
      {plan.es_gratis ? (
        <div>
          <span className="text-4xl font-bold text-green-600">GRATIS</span>
          <p className="text-sm text-gray-600 mt-2">Para siempre</p>
        </div>
      ) : (
        <div>
          <span className="text-4xl font-bold text-gray-900">S/ {monthlyPrice}</span>
          <span className="text-gray-600 ml-2">/mes</span>
          
          {billingCycle === 'anual' && (
            <div className="mt-2">
              <p className="text-sm text-gray-600">S/ {plan.precio_anual} facturado anualmente</p>
              {plan.descuento_anual > 0 && (
                <p className="text-sm text-green-600 font-medium">
                  Ahorras {plan.descuento_anual}%
                </p>
              )}
            </div>
          )}
          
          {plan.dias_prueba > 0 && (
            <p className="text-sm text-blue-600 font-medium mt-2">
              {plan.dias_prueba} días de prueba gratis
            </p>
          )}
        </div>
      )}
    </div>
  </div>
);

const PlanLimits = ({ plan }: { plan: Plan }) => (
  <div className="bg-gray-50 rounded-lg p-4 mb-6 flex-shrink-0">
    <h4 className="text-sm font-semibold text-gray-900 mb-2">Límites incluidos:</h4>
    <div className="grid grid-cols-2 gap-2 text-xs text-gray-600">
      <div>👥 {plan.max_usuarios} usuarios</div>
      <div>🏪 {plan.max_sucursales} sucursal{plan.max_sucursales > 1 ? 'es' : ''}</div>
      <div>🏷️ {plan.max_rubros} rubro{plan.max_rubros > 1 ? 's' : ''}</div>
      <div>📦 {plan.max_productos} productos</div>
    </div>
  </div>
);

const PlanFeatures = ({ features }: { features: string[] }) => (
  <ul className="space-y-3 mb-8 flex-grow">
    {features.map((feature, index) => (
      <li key={index} className="flex items-start">
        <span className="text-red-500 mr-2 flex-shrink-0 mt-1">✓</span>
        <span className="text-gray-600 text-sm">{feature}</span>
      </li>
    ))}
  </ul>
);

const PlanButton = ({ plan }: { plan: Plan }) => (
  <div className="mt-auto">
    <Link href={`/register?plan=${plan.id}`} className="block">
      <button 
        className={`w-full py-3 px-6 rounded-lg font-semibold transition-colors ${
          plan.popular 
            ? 'bg-red-600 hover:bg-red-700 text-white' 
            : plan.es_gratis
            ? 'bg-green-600 hover:bg-green-700 text-white'
            : 'border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white'
        }`}
      >
        {plan.es_gratis ? 'Comenzar Gratis' : 'Probar Gratis'}
      </button>
    </Link>
  </div>
);

export default function PriceCard({ plan, billingCycle }: PriceCardProps) {
  const monthlyPrice = billingCycle === 'mensual' ? plan.precio : Math.floor(plan.precio_anual / 12);

  return (
    <div className={`bg-white rounded-lg shadow-lg p-8 relative transition-all hover:scale-105 flex flex-col h-full ${
      plan.popular ? 'ring-2 ring-red-500 shadow-xl scale-105' : ''
    }`}>
      {plan.popular && (
        <div className="absolute -top-4 left-1/2 transform -translate-x-1/2">
          <span className="bg-red-600 text-white px-4 py-1 rounded-full text-sm font-semibold">
            ⭐ Más Popular
          </span>
        </div>
      )}
      
      <PlanHeader plan={plan} monthlyPrice={monthlyPrice} billingCycle={billingCycle} />
      <PlanLimits plan={plan} />
      <PlanFeatures features={plan.features} />
      <PlanButton plan={plan} />
    </div>
  );
}
