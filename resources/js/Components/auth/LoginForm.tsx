import { FormEventHandler, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import Input from '@/Components/auth/Input';
import Button from '@/Components/auth/Button';
import Checkbox from '@/Components/auth/Checkbox';
import StatusMessage from '@/Components/auth/StatusMessage';

// Declare route function from Ziggy
declare function route(name: string, params?: any): string;

interface LoginFormProps {
    status?: string;
    canResetPassword?: string;
}

export default function LoginForm({ status, canResetPassword }: LoginFormProps) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    useEffect(() => {
        return () => {
            reset('password');
        };
    }, []);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'));
    };

    return (
        <>
            {/* Status Message */}
            {status && (
                <div className="mb-6">
                    <StatusMessage type="success" message={status} />
                </div>
            )}

            <form onSubmit={submit} className="space-y-6">
                {/* Email */}
                <Input
                    id="email"
                    type="email"
                    label="Correo Electrónico"
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                    error={errors.email}
                    autoComplete="username"
                    placeholder="tu@empresa.com"
                    required
                    icon={
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    }
                />

                {/* Password */}
                <Input
                    id="password"
                    type="password"
                    label="Contraseña"
                    value={data.password}
                    onChange={(e) => setData('password', e.target.value)}
                    error={errors.password}
                    autoComplete="current-password"
                    placeholder="••••••••"
                    required
                    icon={
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    }
                />

                {/* Remember & Forgot */}
                <div className="flex items-center justify-between">
                    <Checkbox
                        name="remember"
                        checked={data.remember}
                        onChange={(e) => setData('remember', e.target.checked)}
                        label="Recordarme"
                    />
                    
                    {canResetPassword && (
                        <a
                            href={canResetPassword}
                            className="text-sm font-medium text-red-600 hover:text-red-500 transition-colors"
                        >
                            ¿Olvidaste tu contraseña?
                        </a>
                    )}
                </div>

                {/* Submit Button */}
                <Button
                    type="submit"
                    size="lg"
                    isLoading={processing}
                    className="w-full"
                >
                    {processing ? 'Iniciando sesión...' : 'Iniciar Sesión'}
                </Button>
            </form>
        </>
    );
}
