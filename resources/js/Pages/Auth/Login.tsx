import { Head } from '@inertiajs/react';

// Import modular components
import AuthLayout from '@/Components/auth/AuthLayout';
import LoginForm from '@/Components/auth/LoginForm';
import AuthNavigation from '@/Components/auth/AuthNavigation';
import DemoCredentials from '@/Components/auth/DemoCredentials';

export default function Login({ 
    status, 
    canResetPassword 
}: { 
    status?: string;
    canResetPassword?: string;
}) {
    return (
        <>
            <Head title="Iniciar Sesión" />
            
            <AuthLayout 
                title="¡Bienvenido de vuelta!"
                subtitle="Inicia sesión en tu cuenta para continuar gestionando tu negocio"
            >
                {/* Login Form */}
                <LoginForm 
                    status={status}
                    canResetPassword={canResetPassword}
                />

                {/* Navigation Links */}
                <div className="mt-8">
                    <AuthNavigation type="login" />
                </div>

                {/* Demo Credentials */}
                <DemoCredentials />
            </AuthLayout>
        </>
    );
}
