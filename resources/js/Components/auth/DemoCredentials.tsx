interface DemoCredentialsProps {
    credentials?: {
        email: string;
        password: string;
        description?: string;
    }[];
}

export default function DemoCredentials({ credentials }: DemoCredentialsProps) {
    const defaultCredentials = [
        {
            email: 'admin@donj.com',
            password: 'password123',
            description: 'Tienda Don J'
        },
        {
            email: 'admin@esperanza.com', 
            password: 'password123',
            description: 'Farmacia Esperanza'
        }
    ];

    const creds = credentials || defaultCredentials;

    return (
        <div className="mt-8 p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg border border-amber-200 shadow-sm">
            <h4 className="text-sm font-semibold text-amber-800 mb-3 flex items-center">
                <span className="mr-2">🔑</span>
                Credenciales de prueba
            </h4>
            <div className="space-y-3">
                {creds.map((cred, index) => (
                    <div key={index} className="bg-white/60 p-3 rounded-md border border-amber-100">
                        {cred.description && (
                            <div className="text-xs font-medium text-amber-700 mb-2">
                                {cred.description}
                            </div>
                        )}
                        <div className="grid grid-cols-2 gap-4 text-sm">
                            <div className="flex items-center">
                                <span className="text-amber-700 font-medium w-16">Email:</span>
                                <span className="text-amber-800 font-mono text-xs bg-amber-100 px-2 py-1 rounded flex-1 ml-2">
                                    {cred.email}
                                </span>
                            </div>
                            <div className="flex items-center">
                                <span className="text-amber-700 font-medium w-20">Contraseña:</span>
                                <span className="text-amber-800 font-mono text-xs bg-amber-100 px-2 py-1 rounded flex-1 ml-2">
                                    {cred.password}
                                </span>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
            <div className="mt-3 text-xs text-amber-600 text-center">
                💡 Usa estas credenciales para explorar el sistema
            </div>
        </div>
    );
}
