import { Head } from '@inertiajs/react';

// Import modular components
import AuthLayout from '@/Components/auth/AuthLayout';
import RegisterForm from '@/Components/auth/RegisterForm';
import AuthNavigation from '@/Components/auth/AuthNavigation';

interface Plan {
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

interface Rubro {
    id: number;
    nombre: string;
    modulos_default: any;
}

interface RegisterProps {
    selectedPlanParam?: string;
    planes: Plan[];
    rubros: Rubro[];
}

export default function Register({ selectedPlanParam = 'estandar', planes = [], rubros = [] }: RegisterProps) {
    return (
        <>
            <Head title="Registro - SmartKet" />
            
            <AuthLayout 
                title="Crea tu cuenta"
                subtitle="Únete a miles de empresas que ya confían en SmartKet"
            >
                {/* Register Form */}
                <RegisterForm
                    selectedPlanParam={selectedPlanParam}
                    planes={planes}
                    rubros={rubros}
                />

                {/* Navigation Links */}
                <div className="mt-8">
                    <AuthNavigation type="register" />
                </div>
            </AuthLayout>
        </>
    );
}
