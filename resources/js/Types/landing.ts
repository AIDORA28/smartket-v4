// Types for Landing Page Components

export interface Feature {
  icon?: React.ComponentType<{ className?: string }>;
  icono?: React.ComponentType<{ className?: string }>;
  title?: string;
  titulo?: string;
  description?: string;
  descripcion?: string;
  emoji?: string;
}

export interface Plan {
  id: number;
  name?: string;
  nombre?: string;
  price?: number;
  precio?: number;
  precio_anual?: number;
  currency?: string;
  period?: string;
  description?: string;
  descripcion?: string;
  features?: string[];
  popular?: boolean;
  buttonText?: string;
  buttonColor?: string;
  es_gratis?: boolean;
  descuento_anual?: number;
  dias_prueba?: number;
  max_usuarios?: number;
  max_sucursales?: number;
  max_rubros?: number;
  max_productos?: number;
}

export interface Testimonial {
  id: number;
  name?: string;
  nombre?: string;
  position?: string;
  cargo?: string;
  company?: string;
  empresa?: string;
  content?: string;
  contenido?: string;
  avatar?: string;
  rating?: number;
}

export interface LandingProps {
  features?: Feature[];
  plans?: Plan[];
  planes?: Plan[];
  testimonials?: Testimonial[];
  testimonios?: Testimonial[];
  stats?: {
    users?: number;
    companies?: number;
    sales?: number;
    satisfaction?: number;
  };
}

export interface PriceCardProps {
  plan: Plan;
  popular?: boolean;
  onSelect?: (planId: number) => void;
}

export interface FeaturesSectionProps {
  features: Feature[];
  title?: string;
  subtitle?: string;
}

export interface PricingSectionProps {
  plans?: Plan[];
  planes?: Plan[];
  title?: string;
  subtitle?: string;
}

export interface TestimonialsSectionProps {
  testimonials?: Testimonial[];
  testimonios?: Testimonial[];
  title?: string;
  subtitle?: string;
}

export interface StatsCardProps {
  icon: React.ComponentType<{ className?: string }>;
  label: string;
  value: string | number;
  suffix?: string;
  color?: string;
}
