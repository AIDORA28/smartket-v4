// Tipos compartidos para autenticación
export interface LoginProps {
  status?: string;
  canResetPassword?: string;
}

export interface Plan {
  id: number;
  nombre: string;
  descripcion: string;
  precio_mensual: number;
  precio_anual: number;
  max_usuarios: number;
  max_sucursales: number;
  max_rubros: number;
  max_productos: number;
  dias_prueba: number;
  es_gratis: boolean;
  caracteristicas: string[];
  descuento_anual: number;
}

export interface Rubro {
  id: number;
  nombre: string;
  modulos_default: any;
}

export interface RegisterProps {
  selectedPlanParam?: string;
  planes: Plan[];
  rubros: Rubro[];
}

export interface AuthLayoutProps {
  children: React.ReactNode;
  title: string;
  subtitle?: string;
}

export interface LoginFormProps {
  status?: string;
  canResetPassword?: string;
}

export interface RegisterFormProps {
  selectedPlanParam?: string;
  planes: Plan[];
  rubros: Rubro[];
}
