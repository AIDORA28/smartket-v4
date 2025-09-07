import React from 'react';
import { Link } from '@inertiajs/react';

export default function CTASection() {
  return (
    <section className="bg-red-600 text-white py-20">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 className="text-3xl font-bold mb-4">
          ¿Listo para hacer crecer tu negocio?
        </h2>
        <p className="text-xl mb-8 text-red-100">
          Únete a cientos de empresas que ya están usando SmartKet para 
          gestionar su negocio de manera inteligente.
        </p>
        
        <Link href="/register">
          <button className="bg-white text-red-600 hover:bg-gray-100 px-8 py-3 rounded-lg font-bold text-lg transition-colors mr-4">
            🚀 Comenzar Prueba Gratis
          </button>
        </Link>
        
        <p className="text-sm text-red-100 mt-4">
          15 días gratis • Sin tarjeta de crédito requerida • Soporte incluido
        </p>
      </div>
    </section>
  );
}
