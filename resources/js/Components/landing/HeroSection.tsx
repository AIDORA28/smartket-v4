import React from 'react';
import { Link } from '@inertiajs/react';

const HeroContent = () => (
  <div>
    <h1 className="text-4xl lg:text-6xl font-bold mb-6">
      El ERP más <span className="text-yellow-300">fácil de usar</span> para tu negocio
    </h1>
    <p className="text-xl mb-8 text-red-100">
      Diseñado especialmente para pequeñas y medianas empresas que quieren 
      un sistema profesional sin complicaciones. ¡Por fin un ERP que todos pueden usar!
    </p>
    
    <div className="flex flex-col sm:flex-row gap-4 mb-8">
      <Link href="/register">
        <button className="w-full sm:w-auto bg-white text-red-700 hover:bg-gray-100 px-8 py-3 rounded-lg font-bold text-lg transition-colors">
          🚀 Comenzar Gratis
        </button>
      </Link>
      <button 
        className="w-full sm:w-auto border-2 border-white text-white hover:bg-white hover:text-red-700 px-8 py-3 rounded-lg font-bold text-lg transition-colors"
        onClick={() => document.getElementById('caracteristicas')?.scrollIntoView({ behavior: 'smooth' })}
      >
        📋 Ver Características
      </button>
    </div>
    
    <div className="flex items-center text-sm text-red-100">
      <span className="mr-2">✨</span>
      <span>Sin permanencia • Prueba 15 días gratis • Soporte incluido</span>
    </div>
  </div>
);

const HeroVideo = () => (
  <div className="relative">
    <div className="bg-white rounded-lg shadow-2xl p-2 transform rotate-3 hover:rotate-0 transition-transform duration-300">
      <video 
        autoPlay 
        muted 
        loop
        playsInline
        className="w-full rounded-lg"
        poster="/img/image.png"
      >
        <source src="/video/Minimarket.mp4" type="video/mp4" />
        Tu navegador no soporta el elemento video.
      </video>
      <div className="absolute -bottom-2 -right-2 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-lg">
        ▶ Demo en vivo
      </div>
    </div>
  </div>
);

export default function HeroSection() {
  return (
    <section className="bg-gradient-to-r from-red-700 to-red-800 text-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <HeroContent />
          <HeroVideo />
        </div>
      </div>
    </section>
  );
}
