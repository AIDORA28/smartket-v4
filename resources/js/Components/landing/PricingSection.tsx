import React, { useState } from 'react';
import { Plan } from '@/Types/landing';
import PriceToggle from './PriceToggle';
import PriceCard from './PriceCard';

interface PricingSectionProps {
  planes: Plan[];
}

const PricingHeader = () => (
  <div className="text-center mb-12">
    <h2 className="text-3xl font-bold text-gray-900 mb-4">
      Precios transparentes y justos
    </h2>
    <p className="text-xl text-gray-600 mb-8">
      Elige el plan que mejor se adapte a tu negocio. Todos incluyen prueba gratuita.
    </p>
  </div>
);

export default function PricingSection({ planes }: PricingSectionProps) {
  const [billingCycle, setBillingCycle] = useState<'mensual' | 'anual'>('mensual');

  return (
    <section id="precios" className="py-20 bg-gray-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <PricingHeader />
        
        <div className="flex justify-center mb-12">
          <PriceToggle 
            billingCycle={billingCycle} 
            onToggle={setBillingCycle} 
          />
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {planes.map((plan) => (
            <PriceCard 
              key={plan.id} 
              plan={plan} 
              billingCycle={billingCycle} 
            />
          ))}
        </div>
      </div>
    </section>
  );
}
