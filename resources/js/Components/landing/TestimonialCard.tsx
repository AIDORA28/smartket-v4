import React from 'react';

interface Testimonio {
  nombre: string;
  negocio: string;
  testimonio: string;
  avatar: string;
  rating: number;
}

interface TestimonialCardProps {
  testimonio: Testimonio;
}

export default function TestimonialCard({ testimonio }: TestimonialCardProps) {
  return (
    <div className="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-all">
      <div className="flex mb-4">
        {Array.from({ length: testimonio.rating }).map((_, i) => (
          <span key={i} className="text-yellow-400 text-xl">⭐</span>
        ))}
      </div>
      
      <blockquote className="text-gray-600 mb-6 italic">
        "{testimonio.testimonio}"
      </blockquote>
      
      <div className="flex items-center">
        <img 
          src={testimonio.avatar}
          alt={testimonio.nombre}
          className="w-12 h-12 rounded-full object-cover mr-4"
        />
        <div>
          <div className="font-semibold text-gray-900">
            {testimonio.nombre}
          </div>
          <div className="text-sm text-red-600 font-medium">
            {testimonio.negocio}
          </div>
        </div>
      </div>
    </div>
  );
}
