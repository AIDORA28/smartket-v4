import { Head } from '@inertiajs/react';
import { RegisterProps } from '@/Types/auth';

import AuthLayout from '@/Components/auth/AuthLayout';
import RegisterForm from '@/Components/auth/RegisterForm';
import AuthNavigation from '@/Components/auth/AuthNavigation';

export default function Register({ selectedPlanParam = 'estandar', planes = [], rubros = [] }: RegisterProps) {
    return (
        <>
            <Head title="Registro - SmartKet" />
            
            <AuthLayout 
                title="Crea tu cuenta"
                subtitle="Únete a miles de empresas que ya confían en SmartKet"
            >
                <RegisterForm
                    selectedPlanParam={selectedPlanParam}
                    planes={planes}
                    rubros={rubros}
                />

                <div className="mt-8">
                    <AuthNavigation type="register" />
                </div>
            </AuthLayout>
        </>
    );
}
