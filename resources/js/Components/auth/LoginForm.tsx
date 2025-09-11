import { FormEventHandler, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import Input from '@/Components/auth/Input';
import Button from '@/Components/auth/Button';
import Checkbox from '@/Components/auth/Checkbox';
import StatusMessage from '@/Components/auth/StatusMessage';
import { LoginFormProps } from '@/Types/auth';

declare function route(name: string, params?: any): string;

const EmailIcon = () => (
  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
  </svg>
);

const PasswordIcon = () => (
  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
  </svg>
);

const RememberSection = ({ 
  remember, 
  onRememberChange, 
  canResetPassword 
}: { 
  remember: boolean; 
  onRememberChange: (checked: boolean) => void; 
  canResetPassword?: string; 
}) => (
  <div className="flex items-center justify-between">
    <Checkbox
      name="remember"
      checked={remember}
      onChange={(e) => onRememberChange(e.target.checked)}
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
);

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
            {status && (
                <div className="mb-6">
                    <StatusMessage type="success" message={status} />
                </div>
            )}

            <form onSubmit={submit} className="space-y-6">
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
                    icon={<EmailIcon />}
                />

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
                    icon={<PasswordIcon />}
                />

                <RememberSection
                    remember={data.remember}
                    onRememberChange={(checked) => setData('remember', checked)}
                    canResetPassword={canResetPassword}
                />

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
