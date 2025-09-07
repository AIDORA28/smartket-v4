import { Link } from '@inertiajs/react';

interface AuthNavigationProps {
    type: 'login' | 'register';
}

export default function AuthNavigation({ type }: AuthNavigationProps) {
    return (
        <div className="space-y-4">
            {/* Login/Register Toggle */}
            <div className="text-center">
                {type === 'login' ? (
                    <p className="text-gray-600">
                        ¿No tienes cuenta?{' '}
                        <Link 
                            href="/register" 
                            className="font-semibold text-red-600 hover:text-red-500 transition-colors"
                        >
                            Regístrate gratis
                        </Link>
                    </p>
                ) : (
                    <p className="text-gray-600">
                        ¿Ya tienes cuenta?{' '}
                        <Link 
                            href="/login" 
                            className="font-semibold text-red-600 hover:text-red-500 transition-colors"
                        >
                            Iniciar sesión
                        </Link>
                    </p>
                )}
            </div>

            {/* Back to Home */}
            <div className="text-center">
                <Link 
                    href="/" 
                    className="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors"
                >
                    <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al inicio
                </Link>
            </div>
        </div>
    );
}
