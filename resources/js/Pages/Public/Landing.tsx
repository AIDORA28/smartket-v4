import React from 'react';
import { Head } from '@inertiajs/react';
import { LandingProps } from '@/Types/landing';

import Navbar from '@/Components/landing/Navbar';
import HeroSection from '@/Components/landing/HeroSection';
import StatsSection from '@/Components/landing/StatsSection';
import FeaturesSection from '@/Components/landing/FeaturesSection';
import PricingSection from '@/Components/landing/PricingSection';
import TestimonialsSection from '@/Components/landing/TestimonialsSection';
import CTASection from '@/Components/landing/CTASection';
import Footer from '@/Components/landing/Footer';

export default function Landing({ planes, features, testimonios }: LandingProps) {
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
