import { FormEventHandler, useEffect, useState } from 'react';
import { useForm } from '@inertiajs/react';
import Input from '@/Components/auth/Input';
import Button from '@/Components/auth/Button';
import Checkbox from '@/Components/auth/Checkbox';
import StatusMessage from '@/Components/auth/StatusMessage';

// Declare route function from Ziggy
declare function route(name: string, params?: any): string;

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

interface RegisterFormProps {
    selectedPlanParam?: string;
    planes: Plan[];
    rubros: Rubro[];
}

export default function RegisterForm({ selectedPlanParam = 'estandar', planes = [], rubros = [] }: RegisterFormProps) {
    const [currentStep, setCurrentStep] = useState(1);
    const [selectedPlanId, setSelectedPlanId] = useState<number>(
        planes.find(p => p.id.toString() === selectedPlanParam || p.nombre.toLowerCase().includes(selectedPlanParam))?.id || planes[1]?.id || 1
    );
    const [selectedRubroId, setSelectedRubroId] = useState<number>(rubros[0]?.id || 1);

    const { data, setData, post, processing, errors, reset } = useForm({
        // Paso 1: Datos personales
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        
        // Paso 2: Datos de la empresa
        empresa_nombre: '',
        empresa_ruc: '',
        tiene_ruc: false,
        
        // Paso 3: Plan y rubro
        plan_id: selectedPlanId,
        rubro_id: selectedRubroId,
        
        // Términos
        terms: false,
    });

    useEffect(() => {
        return () => {
            reset('password', 'password_confirmation');
        };
    }, []);

    useEffect(() => {
        setData(prevData => ({
            ...prevData,
            plan_id: selectedPlanId,
            rubro_id: selectedRubroId
        }));
    }, [selectedPlanId, selectedRubroId]);

    const validateStep = (step: number): boolean => {
        switch (step) {
            case 1:
                return !!(data.name && data.email && data.password && data.password_confirmation);
            case 2:
                return !!(data.empresa_nombre && (!data.tiene_ruc || data.empresa_ruc));
            case 3:
                return !!(data.terms && data.plan_id && data.rubro_id);
            default:
                return true;
        }
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (currentStep < 3) {
            if (validateStep(currentStep)) {
                setCurrentStep(currentStep + 1);
            }
            return;
        }

        post(route('register'));
    };

    const goBack = () => {
        if (currentStep > 1) {
            setCurrentStep(currentStep - 1);
        }
    };

    const currentPlan = planes.find(p => p.id === selectedPlanId);
    const currentRubro = rubros.find(r => r.id === selectedRubroId);

    const renderStepContent = () => {
        switch (currentStep) {
            case 1:
                return (
                    <div className="space-y-6">
                        <div className="text-center mb-8">
                            <h3 className="text-xl font-semibold text-gray-900 mb-2">Datos Personales</h3>
                            <p className="text-gray-600">Información del administrador principal</p>
                        </div>

                        <Input
                            id="name"
                            type="text"
                            label="Nombre Completo"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            error={errors.name}
                            placeholder="Juan Pérez"
                            required
                            icon={
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            }
                        />

                        <Input
                            id="email"
                            type="email"
                            label="Correo Electrónico"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            error={errors.email}
                            placeholder="juan@empresa.com"
                            required
                            icon={
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            }
                        />

                        <Input
                            id="password"
                            type="password"
                            label="Contraseña"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            error={errors.password}
                            placeholder="••••••••"
                            required
                            icon={
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            }
                        />

                        <Input
                            id="password_confirmation"
                            type="password"
                            label="Confirmar Contraseña"
                            value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                            error={errors.password_confirmation}
                            placeholder="••••••••"
                            required
                            icon={
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            }
                        />
                    </div>
                );

            case 2:
                return (
                    <div className="space-y-6">
                        <div className="text-center mb-8">
                            <h3 className="text-xl font-semibold text-gray-900 mb-2">Datos de la Empresa</h3>
                            <p className="text-gray-600">Información de tu negocio</p>
                        </div>

                        <Input
                            id="empresa_nombre"
                            type="text"
                            label="Nombre de la Empresa"
                            value={data.empresa_nombre}
                            onChange={(e) => setData('empresa_nombre', e.target.value)}
                            error={errors.empresa_nombre}
                            placeholder="Panadería San Miguel"
                            required
                            icon={
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            }
                        />

                        <div className="space-y-4">
                            <Checkbox
                                checked={data.tiene_ruc}
                                onChange={(e) => setData('tiene_ruc', e.target.checked)}
                                label="Mi empresa tiene RUC"
                            />

                            {data.tiene_ruc && (
                                <Input
                                    id="empresa_ruc"
                                    type="text"
                                    label="RUC"
                                    value={data.empresa_ruc}
                                    onChange={(e) => setData('empresa_ruc', e.target.value)}
                                    error={errors.empresa_ruc}
                                    placeholder="20123456789"
                                    maxLength={11}
                                    icon={
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    }
                                />
                            )}
                        </div>
                    </div>
                );

            case 3:
                return (
                    <div className="space-y-8">
                        <div className="text-center mb-8">
                            <h3 className="text-xl font-semibold text-gray-900 mb-2">Plan y Tipo de Negocio</h3>
                            <p className="text-gray-600">Selecciona tu plan y rubro empresarial</p>
                        </div>

                        {/* Plan Selection */}
                        <div>
                            <h4 className="text-lg font-medium text-gray-900 mb-4">Selecciona tu Plan</h4>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                {planes.map((plan) => (
                                    <div
                                        key={plan.id}
                                        className={`p-4 border rounded-lg cursor-pointer transition-all ${
                                            selectedPlanId === plan.id
                                                ? 'border-red-500 bg-red-50 ring-2 ring-red-200'
                                                : 'border-gray-200 hover:border-gray-300 hover:shadow-md'
                                        }`}
                                        onClick={() => setSelectedPlanId(plan.id)}
                                    >
                                        <div className="text-center">
                                            <h5 className="font-semibold text-gray-900 mb-2">{plan.nombre}</h5>
                                            <div className="mb-3">
                                                {plan.es_gratis ? (
                                                    <div className="text-2xl font-bold text-green-600">GRATIS</div>
                                                ) : (
                                                    <div>
                                                        <div className="text-2xl font-bold text-gray-900">S/ {plan.precio_mensual}</div>
                                                        <div className="text-sm text-gray-500">/mes</div>
                                                        {plan.dias_prueba > 0 && (
                                                            <div className="text-xs text-blue-600 font-medium">
                                                                {plan.dias_prueba} días gratis
                                                            </div>
                                                        )}
                                                    </div>
                                                )}
                                            </div>
                                            <div className="text-sm text-gray-600 space-y-1">
                                                <div>👥 {plan.max_usuarios} usuarios</div>
                                                <div>🏪 {plan.max_sucursales} sucursal{plan.max_sucursales > 1 ? 'es' : ''}</div>
                                                <div>🏷️ {plan.max_rubros} rubro{plan.max_rubros > 1 ? 's' : ''}</div>
                                                <div>📦 {plan.max_productos.toLocaleString()} productos</div>
                                            </div>
                                            <p className="text-xs text-gray-500 mt-2">{plan.descripcion}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Rubro Selection */}
                        <div>
                            <h4 className="text-lg font-medium text-gray-900 mb-4">Tipo de Negocio</h4>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {rubros.map((rubro) => (
                                    <div
                                        key={rubro.id}
                                        className={`p-4 border rounded-lg cursor-pointer transition-all ${
                                            selectedRubroId === rubro.id
                                                ? 'border-red-500 bg-red-50 ring-2 ring-red-200'
                                                : 'border-gray-200 hover:border-gray-300 hover:shadow-md'
                                        }`}
                                        onClick={() => setSelectedRubroId(rubro.id)}
                                    >
                                        <div className="flex items-start">
                                            <input
                                                type="radio"
                                                name="rubro"
                                                value={rubro.id}
                                                checked={selectedRubroId === rubro.id}
                                                onChange={() => setSelectedRubroId(rubro.id)}
                                                className="mt-1 text-red-600 focus:ring-red-500"
                                            />
                                            <div className="ml-3">
                                                <h5 className="font-semibold text-gray-900">{rubro.nombre}</h5>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Terms */}
                        <div className="border-t pt-6">
                            <Checkbox
                                checked={data.terms}
                                onChange={(e) => setData('terms', e.target.checked)}
                                label="Acepto los términos y condiciones y la política de privacidad"
                            />
                            {errors.terms && <p className="mt-1 text-sm text-red-600">{errors.terms}</p>}
                        </div>

                        {/* Summary */}
                        <div className="bg-gray-50 p-4 rounded-lg">
                            <h5 className="font-medium text-gray-900 mb-2">Resumen de tu registro:</h5>
                            <div className="text-sm text-gray-600 space-y-1">
                                <div>📧 <strong>Email:</strong> {data.email}</div>
                                <div>🏢 <strong>Empresa:</strong> {data.empresa_nombre}</div>
                                <div>📋 <strong>Plan:</strong> {currentPlan?.nombre}</div>
                                <div>🏷️ <strong>Rubro:</strong> {currentRubro?.nombre}</div>
                            </div>
                        </div>
                    </div>
                );

            default:
                return null;
        }
    };

    return (
        <>
            {/* Progress Steps */}
            <div className="mb-8">
                <div className="flex items-center justify-between mb-4">
                    {[1, 2, 3].map((step) => (
                        <div key={step} className="flex items-center flex-1">
                            <div
                                className={`w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold transition-colors ${
                                    step <= currentStep
                                        ? 'bg-red-600 text-white'
                                        : 'bg-gray-200 text-gray-500'
                                }`}
                            >
                                {step}
                            </div>
                            {step < 3 && (
                                <div
                                    className={`flex-1 h-1 mx-4 rounded transition-colors ${
                                        step < currentStep ? 'bg-red-600' : 'bg-gray-200'
                                    }`}
                                />
                            )}
                        </div>
                    ))}
                </div>
                <div className="text-center text-sm text-gray-600">
                    Paso {currentStep} de 3
                </div>
            </div>

            {/* Error Messages */}
            {Object.keys(errors).length > 0 && !errors.name && !errors.email && !errors.password && !errors.password_confirmation && (
                <div className="mb-6">
                    <StatusMessage type="error" message="Por favor corrige los errores en el formulario" />
                </div>
            )}

            <form onSubmit={submit} className="space-y-6">
                {renderStepContent()}

                <div className="flex justify-between pt-6">
                    {currentStep > 1 && (
                        <Button
                            type="button"
                            variant="outline"
                            onClick={goBack}
                            className="flex items-center"
                        >
                            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Anterior
                        </Button>
                    )}
                    
                    <Button
                        type="submit"
                        size="lg"
                        isLoading={processing}
                        disabled={!validateStep(currentStep)}
                        className={`${currentStep === 1 ? 'w-full' : 'ml-auto'} flex items-center`}
                    >
                        {currentStep < 3 ? (
                            <>
                                Siguiente
                                <svg className="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </>
                        ) : (
                            '🚀 Crear mi cuenta'
                        )}
                    </Button>
                </div>
            </form>
        </>
    );
}
