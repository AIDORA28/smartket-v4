import React from 'react';
import { Link } from '@inertiajs/react';

export default function Footer() {
  return (
    <footer className="bg-gray-900 text-white py-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <div className="flex items-center mb-4">
              <img 
                src="/img/SmartKet.svg" 
                alt="SmartKet" 
                className="h-8 w-auto"
              />
              <span className="ml-2 text-xl font-bold">SmartKet</span>
            </div>
            <p className="text-gray-400">
              ERP intuitivo diseñado para pequeñas y medianas empresas.
            </p>
          </div>
          
          <div>
            <h4 className="font-semibold mb-4 text-red-400">Producto</h4>
            <ul className="space-y-2 text-gray-400">
              <li><a href="#caracteristicas" className="hover:text-white transition-colors">Características</a></li>
              <li><a href="#precios" className="hover:text-white transition-colors">Precios</a></li>
              <li><Link href="/login" className="hover:text-white transition-colors">Demo</Link></li>
            </ul>
          </div>
          
          <div>
            <h4 className="font-semibold mb-4 text-red-400">Soporte</h4>
            <ul className="space-y-2 text-gray-400">
              <li><a href="#" className="hover:text-white transition-colors">Centro de Ayuda</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Contacto</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Capacitación</a></li>
            </ul>
          </div>
          
          <div>
            <h4 className="font-semibold mb-4 text-red-400">Empresa</h4>
            <ul className="space-y-2 text-gray-400">
              <li><a href="#" className="hover:text-white transition-colors">Acerca de</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Blog</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Términos</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Privacidad</a></li>
            </ul>
          </div>
        </div>
        
        <div className="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
          <p>&copy; 2025 SmartKet. Todos los derechos reservados.</p>
        </div>
      </div>
    </footer>
  );
}
