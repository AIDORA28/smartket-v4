import React from 'react';
import TestimonialCard from './TestimonialCard';

interface Testimonio {
  nombre: string;
  negocio: string;
  testimonio: string;
  avatar: string;
  rating: number;
}

interface TestimonialsSectionProps {
  testimonios: Testimonio[];
}

export default function TestimonialsSection({ testimonios }: TestimonialsSectionProps) {
  return (
    <section id="testimonios" className="py-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-3xl font-bold text-gray-900 mb-4">
            Lo que dicen nuestros clientes
          </h2>
          <p className="text-xl text-gray-600">
            Más de 500 negocios confían en SmartKet para crecer cada día
          </p>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {testimonios.map((testimonio, index) => (
            <TestimonialCard 
              key={index} 
              testimonio={testimonio} 
            />
          ))}
        </div>
      </div>
    </section>
  );
}
