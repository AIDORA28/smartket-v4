import { Head } from '@inertiajs/react';
import { LoginProps } from '@/Types/auth';

import AuthLayout from '@/Components/auth/AuthLayout';
import LoginForm from '@/Components/auth/LoginForm';
import AuthNavigation from '@/Components/auth/AuthNavigation';

export default function Login({ status, canResetPassword }: LoginProps) {
    return (
        <>
            <Head title="Iniciar Sesión" />
            
            <AuthLayout 
                title="¡Bienvenido de vuelta!"
                subtitle="Inicia sesión en tu cuenta para continuar gestionando tu negocio"
            >
                <LoginForm 
                    status={status}
                    canResetPassword={canResetPassword}
                />

                <div className="mt-8">
                    <AuthNavigation type="login" />
                </div>
            </AuthLayout>
        </>
    );
}
