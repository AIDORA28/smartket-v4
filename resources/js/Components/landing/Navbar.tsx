import React from 'react';
import { Link } from '@inertiajs/react';

const Logo = () => (
  <div className="flex items-center">
    <img 
      src="/img/SmartKet.svg" 
      alt="SmartKet" 
      className="h-10 w-auto transition-transform hover:scale-110"
    />
    <span className="ml-3 text-xl font-bold">
      <span className="text-amber-500 uppercase tracking-wide">SMART</span>
      <span className="text-gray-900 lowercase">ket</span>
    </span>
  </div>
);

const NavLinks = () => (
  <div className="flex items-center space-x-1">
    <a 
      href="#caracteristicas" 
      className="text-gray-600 hover:text-red-600 px-4 py-2 rounded-lg font-medium transition-colors"
    >
      Características
    </a>
    <a 
      href="#precios" 
      className="text-gray-600 hover:text-red-600 px-4 py-2 rounded-lg font-medium transition-colors"
    >
      Precios
    </a>
    <a 
      href="#testimonios" 
      className="text-gray-600 hover:text-red-600 px-4 py-2 rounded-lg font-medium transition-colors"
    >
      Testimonios
    </a>
    <Link href="/login">
      <button className="text-gray-600 hover:text-red-600 px-6 py-2 rounded-lg font-medium transition-colors border border-transparent hover:border-gray-200">
        Iniciar Sesión
      </button>
    </Link>
    <Link href="/register">
      <button className="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
        Prueba Gratis
      </button>
    </Link>
  </div>
);

export default function Navbar() {
  return (
    <nav className="bg-white shadow-sm sticky top-0 z-50 backdrop-blur-sm bg-white/95">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16">
          <Logo />
          <NavLinks />
        </div>
      </div>
    </nav>
  );
}
