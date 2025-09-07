import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Card from '@/Components/Card';
import Button from '@/Components/Button';
import { 
    MagnifyingGlassIcon,
    ArrowLeftIcon,
    ArrowUpIcon,
    ArrowDownIcon,
    AdjustmentsHorizontalIcon
} from '@heroicons/react/24/outline';

interface Movement {
    id: number;
    fecha: string;
    producto: string;
    tipo_movimiento: 'entrada' | 'salida' | 'ajuste';
    cantidad: number;
    costo_unitario: number;
    stock_anterior: number;
    stock_posterior: number;
    referencia_tipo: string;
    observaciones: string;
    usuario: string;
}

interface Product {
    id: number;
    nombre: string;
}

interface Props {
    movements: {
        data: Movement[];
        links: any[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    products: Product[];
    filters: {
        search?: string;
        tipo_movimiento?: string;
        fecha_inicio?: string;
        fecha_fin?: string;
        producto_id?: string;
    };
    sort: {
        sort_by: string;
        sort_direction: string;
    };
    error?: string;
}

const movementTypeColors = {
    entrada: 'bg-green-100 text-green-800',
    salida: 'bg-red-100 text-red-800',
    ajuste: 'bg-yellow-100 text-yellow-800',
};

const movementTypeLabels = {
    entrada: 'Entrada',
    salida: 'Salida',
    ajuste: 'Ajuste',
};

const movementIcons = {
    entrada: ArrowUpIcon,
    salida: ArrowDownIcon,
    ajuste: AdjustmentsHorizontalIcon,
};

export default function InventoryMovements({ movements, products, filters, sort, error }: Props) {
    const [search, setSearch] = useState(filters.search || '');
    const [movementType, setMovementType] = useState(filters.tipo_movimiento || '');
    const [startDate, setStartDate] = useState(filters.fecha_inicio || '');
    const [endDate, setEndDate] = useState(filters.fecha_fin || '');
    const [selectedProduct, setSelectedProduct] = useState(filters.producto_id || '');

    if (error) {
        return (
            <AuthenticatedLayout>
                <Head title="Movimientos de Inventario" />
                <div className="py-12">
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {error}
                        </div>
                    </div>
                </div>
            </AuthenticatedLayout>
        );
    }

    const handleSearch = () => {
        router.get('/inventario/movements', {
            search,
            tipo_movimiento: movementType,
            fecha_inicio: startDate,
            fecha_fin: endDate,
            producto_id: selectedProduct,
            sort_by: sort.sort_by,
            sort_direction: sort.sort_direction,
        }, { preserveState: true });
    };

    const handleSort = (column: string) => {
        const direction = sort.sort_by === column && sort.sort_direction === 'asc' ? 'desc' : 'asc';
        router.get('/inventario/movements', {
            ...filters,
            sort_by: column,
            sort_direction: direction,
        }, { preserveState: true });
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleString('es-ES', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Movimientos de Inventario" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="mb-8 flex items-center justify-between">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">Movimientos de Inventario</h1>
                            <p className="mt-2 text-gray-600">Historial detallado de movimientos de stock</p>
                        </div>
                        <Link href="/inventario">
                            <Button variant="outline">
                                <ArrowLeftIcon className="h-4 w-4 mr-2" />
                                Volver al Inventario
                            </Button>
                        </Link>
                    </div>

                    {/* Filtros */}
                    <Card className="mb-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Buscar
                                </label>
                                <div className="relative">
                                    <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                    <input
                                        type="text"
                                        placeholder="Buscar producto..."
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
                                        className="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                            </div>
                            
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Producto
                                </label>
                                <select
                                    value={selectedProduct}
                                    onChange={(e) => setSelectedProduct(e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Todos los productos</option>
                                    {products.map((product) => (
                                        <option key={product.id} value={product.id}>
                                            {product.nombre}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Tipo
                                </label>
                                <select
                                    value={movementType}
                                    onChange={(e) => setMovementType(e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Todos los tipos</option>
                                    <option value="entrada">Entrada</option>
                                    <option value="salida">Salida</option>
                                    <option value="ajuste">Ajuste</option>
                                </select>
                            </div>
                            
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha Inicio
                                </label>
                                <input
                                    type="date"
                                    value={startDate}
                                    onChange={(e) => setStartDate(e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                            
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha Fin
                                </label>
                                <input
                                    type="date"
                                    value={endDate}
                                    onChange={(e) => setEndDate(e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                        </div>
                        
                        <div className="mt-4 flex justify-end">
                            <Button onClick={handleSearch}>
                                Aplicar Filtros
                            </Button>
                        </div>
                    </Card>

                    {/* Tabla de movimientos */}
                    <Card>
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th 
                                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            onClick={() => handleSort('fecha_movimiento')}
                                        >
                                            Fecha
                                            {sort.sort_by === 'fecha_movimiento' && (
                                                <span className="ml-1">
                                                    {sort.sort_direction === 'asc' ? '↑' : '↓'}
                                                </span>
                                            )}
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Producto
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipo
                                        </th>
                                        <th 
                                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            onClick={() => handleSort('cantidad')}
                                        >
                                            Cantidad
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stock
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Costo Unit.
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Usuario
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Observaciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {movements.data.map((movement) => {
                                        const Icon = movementIcons[movement.tipo_movimiento];
                                        return (
                                            <tr key={movement.id} className="hover:bg-gray-50">
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {formatDate(movement.fecha)}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {movement.producto}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className={`inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${movementTypeColors[movement.tipo_movimiento]}`}>
                                                        <Icon className="h-3 w-3 mr-1" />
                                                        {movementTypeLabels[movement.tipo_movimiento]}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <span className={`font-medium ${
                                                        movement.tipo_movimiento === 'entrada' ? 'text-green-600' :
                                                        movement.tipo_movimiento === 'salida' ? 'text-red-600' : 'text-yellow-600'
                                                    }`}>
                                                        {movement.tipo_movimiento === 'entrada' ? '+' : 
                                                         movement.tipo_movimiento === 'salida' ? '-' : '='}{movement.cantidad}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <div>
                                                        <span className="text-gray-400">Anterior:</span> {movement.stock_anterior}
                                                    </div>
                                                    <div>
                                                        <span className="text-gray-400">Nuevo:</span> <span className="font-medium text-gray-900">{movement.stock_posterior}</span>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    ${movement.costo_unitario.toFixed(2)}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {movement.usuario}
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                                    {movement.observaciones || '-'}
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>

                        {/* Mensaje cuando no hay datos */}
                        {movements.data.length === 0 && (
                            <div className="text-center py-12">
                                <AdjustmentsHorizontalIcon className="mx-auto h-12 w-12 text-gray-400" />
                                <h3 className="mt-2 text-sm font-medium text-gray-900">No hay movimientos</h3>
                                <p className="mt-1 text-sm text-gray-500">
                                    No se encontraron movimientos con los filtros aplicados.
                                </p>
                            </div>
                        )}

                        {/* Paginación */}
                        {movements.last_page > 1 && (
                            <div className="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                                <div className="flex items-center justify-between">
                                    <div className="text-sm text-gray-700">
                                        Mostrando {((movements.current_page - 1) * movements.per_page) + 1} a {Math.min(movements.current_page * movements.per_page, movements.total)} de {movements.total} movimientos
                                    </div>
                                    <div className="flex space-x-2">
                                        {movements.links.map((link, index) => (
                                            link.url && (
                                                <Link
                                                    key={index}
                                                    href={link.url}
                                                    className={`px-3 py-2 text-sm font-medium rounded-md ${
                                                        link.active 
                                                            ? 'bg-blue-600 text-white' 
                                                            : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'
                                                    }`}
                                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                                />
                                            )
                                        ))}
                                    </div>
                                </div>
                            </div>
                        )}
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
