import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Card from '@/Components/Card';
import Button from '@/Components/Button';
// Modular Components
import MetricCard from '@/Components/core/shared/MetricCard';
import ActionCard from '@/Components/core/shared/ActionCard';
import { 
    MagnifyingGlassIcon,
    PlusIcon,
    EyeIcon,
    TruckIcon,
    CheckCircleIcon,
    ClockIcon,
    XMarkIcon,
    DocumentTextIcon,
    BuildingOfficeIcon
} from '@heroicons/react/24/outline';

interface Supplier {
    id: number;
    nombre: string;
    ruc: string;
    telefono?: string;
    email?: string;
}

interface PurchaseItem {
    id: number;
    producto_id: number;
    producto_nombre: string;
    cantidad: number;
    precio_unitario: number;
    subtotal: number;
}

interface Purchase {
    id: number;
    numero_compra: string;
    proveedor_id: number;
    proveedor: Supplier;
    fecha_compra: string;
    fecha_entrega?: string;
    estado: 'pendiente' | 'recibida' | 'cancelada';
    subtotal: number;
    impuestos: number;
    total: number;
    items: PurchaseItem[];
    notas?: string;
    created_at: string;
}

interface PurchasesPageProps {
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
            rol_principal: string;
        };
    };
    purchases: {
        data: Purchase[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    suppliers: Supplier[];
    stats: {
        total_compras: number;
        compras_pendientes: number;
        compras_recibidas: number;
        valor_total_mes: number;
        compras_mes: number;
    };
    filters: {
        search?: string;
        estado?: string;
        proveedor_id?: string;
        date_from?: string;
        date_to?: string;
    };
    error?: string;
}

export default function Purchases({ 
    auth, 
    purchases, 
    suppliers, 
    stats, 
    filters, 
    error 
}: PurchasesPageProps) {
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [statusFilter, setStatusFilter] = useState(filters.estado || 'all');
    const [supplierFilter, setSupplierFilter] = useState(filters.proveedor_id || 'all');
    const [dateFrom, setDateFrom] = useState(filters.date_from || '');
    const [dateTo, setDateTo] = useState(filters.date_to || '');
    const [selectedPurchase, setSelectedPurchase] = useState<Purchase | null>(null);
    const [showDetailModal, setShowDetailModal] = useState(false);

    if (error) {
        return (
            <AuthenticatedLayout>
                <Head title="Compras - Error" />
                <div className="py-6">
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div className="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                            <h2 className="text-lg font-medium text-red-900 mb-2">Error de configuración</h2>
                            <p className="text-red-700">{error}</p>
                        </div>
                    </div>
                </div>
            </AuthenticatedLayout>
        );
    }

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'recibida':
                return 'bg-green-100 text-green-800';
            case 'pendiente':
                return 'bg-yellow-100 text-yellow-800';
            case 'cancelada':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getStatusText = (status: string) => {
        switch (status) {
            case 'recibida':
                return 'Recibida';
            case 'pendiente':
                return 'Pendiente';
            case 'cancelada':
                return 'Cancelada';
            default:
                return status;
        }
    };

    const applyFilters = () => {
        router.get('/purchases', {
            search: searchTerm,
            estado: statusFilter === 'all' ? undefined : statusFilter,
            proveedor_id: supplierFilter === 'all' ? undefined : supplierFilter,
            date_from: dateFrom,
            date_to: dateTo
        }, {
            preserveState: true,
            only: ['purchases']
        });
    };

    const viewPurchaseDetails = (purchase: Purchase) => {
        setSelectedPurchase(purchase);
        setShowDetailModal(true);
    };

    const markAsReceived = (purchaseId: number) => {
        if (confirm('¿Confirmar que se recibió esta compra?')) {
            router.patch(`/purchases/${purchaseId}/receive`, {}, {
                onSuccess: () => {
                    // Mostrar mensaje de éxito
                }
            });
        }
    };

    const cancelPurchase = (purchaseId: number) => {
        if (confirm('¿Estás seguro de cancelar esta compra?')) {
            router.patch(`/purchases/${purchaseId}/cancel`, {}, {
                onSuccess: () => {
                    // Mostrar mensaje de éxito
                }
            });
        }
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                            🛒 Gestión de Compras
                        </h2>
                        <p className="text-gray-600 text-sm mt-1">
                            Control completo de órdenes de compra y proveedores
                        </p>
                    </div>
                    <div className="flex gap-3">
                        <Link href="/purchases/create">
                            <Button>
                                <PlusIcon className="w-4 h-4 mr-2" />
                                Nueva Compra
                            </Button>
                        </Link>
                        <Link href="/suppliers">
                            <Button variant="secondary">
                                <BuildingOfficeIcon className="w-4 h-4 mr-2" />
                                Proveedores
                            </Button>
                        </Link>
                    </div>
                </div>
            }
        >
            <Head title="Compras" />
            
            <div className="py-6">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    
                    {/* Estadísticas con MetricCard */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        <MetricCard
                            title="🛒 Total Compras"
                            value={stats.total_compras.toString()}
                            emoji="🛒"
                            color="blue"
                            subtitle="órdenes registradas"
                        />
                        
                        <MetricCard
                            title="⏳ Pendientes"
                            value={stats.compras_pendientes.toString()}
                            emoji="⏳"
                            color="orange"
                            subtitle="por recibir"
                        />
                        
                        <MetricCard
                            title="✅ Recibidas"
                            value={stats.compras_recibidas.toString()}
                            emoji="✅"
                            color="green"
                            subtitle="completadas"
                        />
                        
                        <MetricCard
                            title="💰 Valor Mensual"
                            value={`S/. ${stats.valor_total_mes.toLocaleString()}`}
                            emoji="💰"
                            color="purple"
                            subtitle="compras del mes"
                        />
                        
                        <MetricCard
                            title="📈 Compras Mes"
                            value={stats.compras_mes.toString()}
                            emoji="📈"
                            color="indigo"
                            subtitle="este mes"
                        />
                    </div>

                    {/* Filtros y Búsqueda */}
                    <Card>
                        <div className="p-6">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">Filtros y Búsqueda</h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                
                                {/* Búsqueda */}
                                <div className="relative">
                                    <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input
                                        type="text"
                                        placeholder="Buscar compras..."
                                        className="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        value={searchTerm}
                                        onChange={(e) => setSearchTerm(e.target.value)}
                                    />
                                </div>

                                {/* Estado */}
                                <select
                                    value={statusFilter}
                                    onChange={(e) => setStatusFilter(e.target.value)}
                                    className="border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="all">Todos los estados</option>
                                    <option value="pendiente">Pendientes</option>
                                    <option value="recibida">Recibidas</option>
                                    <option value="cancelada">Canceladas</option>
                                </select>

                                {/* Proveedor */}
                                <select
                                    value={supplierFilter}
                                    onChange={(e) => setSupplierFilter(e.target.value)}
                                    className="border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="all">Todos los proveedores</option>
                                    {suppliers.map((supplier) => (
                                        <option key={supplier.id} value={supplier.id}>
                                            {supplier.nombre}
                                        </option>
                                    ))}
                                </select>

                                {/* Fecha desde */}
                                <input
                                    type="date"
                                    value={dateFrom}
                                    onChange={(e) => setDateFrom(e.target.value)}
                                    className="border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Desde"
                                />

                                {/* Fecha hasta */}
                                <input
                                    type="date"
                                    value={dateTo}
                                    onChange={(e) => setDateTo(e.target.value)}
                                    className="border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Hasta"
                                />
                            </div>
                            
                            <div className="mt-4 flex justify-end">
                                <Button onClick={applyFilters}>
                                    Aplicar Filtros
                                </Button>
                            </div>
                        </div>
                    </Card>

                    {/* Lista de Compras */}
                    <Card>
                        <div className="p-6">
                            <div className="flex justify-between items-center mb-4">
                                <h3 className="text-lg font-medium text-gray-900">
                                    Órdenes de Compra ({purchases.total})
                                </h3>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Número/Fecha
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Proveedor
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Estado
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Total
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Fecha Entrega
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Acciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {purchases.data.map((purchase) => (
                                            <tr key={purchase.id} className="hover:bg-gray-50">
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div>
                                                        <div className="text-sm font-medium text-gray-900">
                                                            {purchase.numero_compra}
                                                        </div>
                                                        <div className="text-sm text-gray-500">
                                                            {new Date(purchase.fecha_compra).toLocaleDateString()}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div>
                                                        <div className="text-sm font-medium text-gray-900">
                                                            {purchase.proveedor.nombre}
                                                        </div>
                                                        <div className="text-sm text-gray-500">
                                                            {purchase.proveedor.ruc}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(purchase.estado)}`}>
                                                        {getStatusText(purchase.estado)}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    S/. {purchase.total.toLocaleString()}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {purchase.fecha_entrega 
                                                        ? new Date(purchase.fecha_entrega).toLocaleDateString()
                                                        : 'No definida'
                                                    }
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => viewPurchaseDetails(purchase)}
                                                    >
                                                        <EyeIcon className="w-4 h-4 mr-1" />
                                                        Ver
                                                    </Button>
                                                    
                                                    {purchase.estado === 'pendiente' && (
                                                        <>
                                                            <Button
                                                                variant="outline"
                                                                size="sm"
                                                                onClick={() => markAsReceived(purchase.id)}
                                                                className="text-green-600 border-green-600 hover:bg-green-50"
                                                            >
                                                                <CheckCircleIcon className="w-4 h-4 mr-1" />
                                                                Recibir
                                                            </Button>
                                                            <Button
                                                                variant="outline"
                                                                size="sm"
                                                                onClick={() => cancelPurchase(purchase.id)}
                                                                className="text-red-600 border-red-600 hover:bg-red-50"
                                                            >
                                                                <XMarkIcon className="w-4 h-4 mr-1" />
                                                                Cancelar
                                                            </Button>
                                                        </>
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* Paginación */}
                            {purchases.total === 0 && (
                                <div className="text-center py-8">
                                    <TruckIcon className="mx-auto h-12 w-12 text-gray-400" />
                                    <h3 className="mt-2 text-sm font-medium text-gray-900">No hay compras</h3>
                                    <p className="mt-1 text-sm text-gray-500">
                                        Comienza creando una nueva orden de compra.
                                    </p>
                                    <div className="mt-6">
                                        <Link href="/purchases/create">
                                            <Button>
                                                <PlusIcon className="w-4 h-4 mr-2" />
                                                Nueva Compra
                                            </Button>
                                        </Link>
                                    </div>
                                </div>
                            )}
                        </div>
                    </Card>

                    {/* Modal de Detalles de Compra */}
                    {showDetailModal && selectedPurchase && (
                        <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                            <div className="relative top-20 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
                                <div className="mt-3">
                                    <div className="flex justify-between items-center mb-4">
                                        <h3 className="text-lg font-medium text-gray-900">
                                            Detalles de Compra: {selectedPurchase.numero_compra}
                                        </h3>
                                        <button
                                            onClick={() => setShowDetailModal(false)}
                                            className="text-gray-400 hover:text-gray-600"
                                        >
                                            <XMarkIcon className="w-6 h-6" />
                                        </button>
                                    </div>
                                    
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <div>
                                            <h4 className="font-medium text-gray-900 mb-2">Información de Compra</h4>
                                            <div className="space-y-2 text-sm">
                                                <div><span className="font-medium">Número:</span> {selectedPurchase.numero_compra}</div>
                                                <div><span className="font-medium">Fecha:</span> {new Date(selectedPurchase.fecha_compra).toLocaleDateString()}</div>
                                                <div><span className="font-medium">Estado:</span> 
                                                    <span className={`ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(selectedPurchase.estado)}`}>
                                                        {getStatusText(selectedPurchase.estado)}
                                                    </span>
                                                </div>
                                                {selectedPurchase.fecha_entrega && (
                                                    <div><span className="font-medium">Entrega:</span> {new Date(selectedPurchase.fecha_entrega).toLocaleDateString()}</div>
                                                )}
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <h4 className="font-medium text-gray-900 mb-2">Información del Proveedor</h4>
                                            <div className="space-y-2 text-sm">
                                                <div><span className="font-medium">Nombre:</span> {selectedPurchase.proveedor.nombre}</div>
                                                <div><span className="font-medium">RUC:</span> {selectedPurchase.proveedor.ruc}</div>
                                                {selectedPurchase.proveedor.telefono && (
                                                    <div><span className="font-medium">Teléfono:</span> {selectedPurchase.proveedor.telefono}</div>
                                                )}
                                                {selectedPurchase.proveedor.email && (
                                                    <div><span className="font-medium">Email:</span> {selectedPurchase.proveedor.email}</div>
                                                )}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="mb-6">
                                        <h4 className="font-medium text-gray-900 mb-2">Items de Compra</h4>
                                        <div className="overflow-x-auto">
                                            <table className="min-w-full divide-y divide-gray-200">
                                                <thead className="bg-gray-50">
                                                    <tr>
                                                        <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                                        <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                                        <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                                        <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody className="bg-white divide-y divide-gray-200">
                                                    {selectedPurchase.items.map((item) => (
                                                        <tr key={item.id}>
                                                            <td className="px-4 py-2 text-sm text-gray-900">{item.producto_nombre}</td>
                                                            <td className="px-4 py-2 text-sm text-gray-900">{item.cantidad}</td>
                                                            <td className="px-4 py-2 text-sm text-gray-900">S/. {item.precio_unitario}</td>
                                                            <td className="px-4 py-2 text-sm text-gray-900">S/. {item.subtotal}</td>
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div className="flex justify-between items-center">
                                        <div>
                                            {selectedPurchase.notas && (
                                                <div>
                                                    <span className="font-medium text-gray-900">Notas:</span>
                                                    <p className="text-sm text-gray-600 mt-1">{selectedPurchase.notas}</p>
                                                </div>
                                            )}
                                        </div>
                                        
                                        <div className="text-right">
                                            <div className="space-y-1">
                                                <div className="text-sm"><span className="font-medium">Subtotal:</span> S/. {selectedPurchase.subtotal}</div>
                                                <div className="text-sm"><span className="font-medium">Impuestos:</span> S/. {selectedPurchase.impuestos}</div>
                                                <div className="text-lg font-bold"><span className="font-medium">Total:</span> S/. {selectedPurchase.total}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}