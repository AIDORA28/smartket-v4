import React from 'react';
import FeatureCard from './FeatureCard';

interface Feature {
  titulo: string;
  descripcion: string;
  icono: string;
}

interface FeaturesSectionProps {
  features: Feature[];
}

export default function FeaturesSection({ features }: FeaturesSectionProps) {
  return (
    <section id="caracteristicas" className="py-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-3xl font-bold text-gray-900 mb-4">
            ¿Por qué elegir SmartKet?
          </h2>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Hemos diseñado cada función pensando en la facilidad de uso, 
            para que puedas enfocarte en hacer crecer tu negocio.
          </p>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {features.map((feature, index) => (
            <FeatureCard
              key={index}
              icono={feature.icono}
              titulo={feature.titulo}
              descripcion={feature.descripcion}
            />
          ))}
        </div>
      </div>
    </section>
  );
}
