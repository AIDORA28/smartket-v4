import React from 'react';

interface FeatureCardProps {
  icono: string;
  titulo: string;
  descripcion: string;
}

export default function FeatureCard({ icono, titulo, descripcion }: FeatureCardProps) {
  return (
    <div className="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition-all hover:scale-105 border-t-4 border-red-600">
      <div className="text-4xl mb-4">{icono}</div>
      <h3 className="text-xl font-bold mb-4 text-gray-900">
        {titulo}
      </h3>
      <p className="text-gray-600">
        {descripcion}
      </p>
    </div>
  );
}
