import React from 'react';
import { Link } from '@inertiajs/react';
import { AuthLayoutProps } from '@/Types/auth';

const AuthBranding = () => (
  <div className="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-red-600 to-red-800 relative overflow-hidden">
    <div className="absolute inset-0 bg-black/20"></div>
    <div className="relative z-10 flex flex-col justify-center px-12 text-white">
      <div className="mb-8">
        <Link href="/" className="inline-flex items-center">
          <img 
            src="/img/SmartKet.svg" 
            alt="SmartKet" 
            className="h-12 w-auto filter brightness-0 invert"
          />
          <span className="ml-4 text-3xl font-bold">
            <span className="text-amber-300 uppercase tracking-wide">SMART</span>
            <span className="lowercase">ket</span>
          </span>
        </Link>
      </div>
      
      <div className="max-w-md">
        <h1 className="text-4xl font-bold mb-4">
          Gestiona tu negocio de manera inteligente
        </h1>
        <p className="text-xl text-red-100 leading-relaxed">
          Control total de inventario, ventas y clientes en una sola plataforma.
        </p>
      </div>
      
      <div className="mt-12 space-y-4">
        <div className="flex items-center text-red-100">
          <div className="w-2 h-2 bg-amber-300 rounded-full mr-3"></div>
          <span>Control de inventario en tiempo real</span>
        </div>
        <div className="flex items-center text-red-100">
          <div className="w-2 h-2 bg-amber-300 rounded-full mr-3"></div>
          <span>Reportes detallados y analytics</span>
        </div>
        <div className="flex items-center text-red-100">
          <div className="w-2 h-2 bg-amber-300 rounded-full mr-3"></div>
          <span>Soporte 24/7 especializado</span>
        </div>
      </div>
    </div>
    
    <div className="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-20 translate-x-20"></div>
    <div className="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full translate-y-16 -translate-x-16"></div>
  </div>
);

const MobileLogo = () => (
  <div className="lg:hidden text-center mb-8">
    <Link href="/" className="inline-flex items-center">
      <img 
        src="/img/SmartKet.svg" 
        alt="SmartKet" 
        className="h-10 w-auto"
      />
      <span className="ml-3 text-2xl font-bold">
        <span className="text-amber-500 uppercase tracking-wide">SMART</span>
        <span className="text-gray-900 lowercase">ket</span>
      </span>
    </Link>
  </div>
);

const FormHeader = ({ title, subtitle }: { title: string; subtitle?: string }) => (
  <div className="text-center lg:text-left">
    <h2 className="text-3xl font-bold text-gray-900 mb-2">
      {title}
    </h2>
    {subtitle && (
      <p className="text-gray-600 mb-8">
        {subtitle}
      </p>
    )}
  </div>
);

export default function AuthLayout({ children, title, subtitle }: AuthLayoutProps) {
  return (
    <div className="min-h-screen flex">
      <AuthBranding />

      <div className="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24 bg-gray-50">
        <div className="mx-auto w-full max-w-sm lg:max-w-md">
          <MobileLogo />
          <FormHeader title={title} subtitle={subtitle} />
          {children}
        </div>
      </div>
    </div>
  );
}
