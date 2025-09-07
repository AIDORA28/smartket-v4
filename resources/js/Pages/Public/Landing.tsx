import React from 'react';
import { Head } from '@inertiajs/react';
import Navbar from '../../Components/landing/Navbar';
import HeroSection from '../../Components/landing/HeroSection';
import StatsSection from '../../Components/landing/StatsSection';
import FeaturesSection from '../../Components/landing/FeaturesSection';
import PricingSection from '../../Components/landing/PricingSection';
import TestimonialsSection from '../../Components/landing/TestimonialsSection';
import CTASection from '../../Components/landing/CTASection';
import Footer from '../../Components/landing/Footer';

interface Plan {
  id: string;
  nombre: string;
  precio: number;
  precio_anual: number;
  descripcion: string;
  features: string[];
  popular: boolean;
  max_usuarios: number;
  max_sucursales: number;
  max_rubros: number;
  max_productos: number;
  dias_prueba: number;
  es_gratis: boolean;
  descuento_anual: number;
}

interface Feature {
  titulo: string;
  descripcion: string;
  icono: string;
}

interface Testimonio {
  nombre: string;
  negocio: string;
  testimonio: string;
  avatar: string;
  rating: number;
}

interface Props {
  planes: Plan[];
  features: Feature[];
  testimonios: Testimonio[];
}

export default function Landing({ planes, features, testimonios }: Props) {
  return (
    <>
      <Head title="SmartKet - ERP Intuitivo para PyMEs" />
      
      <Navbar />
      <HeroSection />
      <StatsSection />
      <FeaturesSection features={features} />
      <PricingSection planes={planes} />
      <TestimonialsSection testimonios={testimonios} />
      <CTASection />
      <Footer />
    </>
  );
}
