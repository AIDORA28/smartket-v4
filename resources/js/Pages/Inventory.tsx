import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Card from '@/Components/Card';
import StatsCard from '@/Components/StatsCard';
import Button from '@/Components/Button';
import { 
    MagnifyingGlassIcon,
    AdjustmentsHorizontalIcon,
    PlusIcon,
    MinusIcon,
    ExclamationTriangleIcon,
    ChartBarIcon,
    ClockIcon
} from '@heroicons/react/24/outline';

interface Product {
    id: number;
    nombre: string;
    codigo_interno: string;
    codigo_barra: string;
    categoria: string;
    categoria_id: number;
    precio_costo: number;
    precio_venta: number;
    stock_actual: number;
    stock_disponible: number;
    stock_reservado: number;
    stock_minimo: number;
    stock_maximo: number;
    estado_stock: 'normal' | 'bajo' | 'sin_stock' | 'exceso';
    valor_inventario: number;
    imagen?: string;
}

interface Category {
    id: number;
    nombre: string;
}

interface Stats {
    total_productos: number;
    productos_stock_bajo: number;
    productos_sin_stock: number;
    valor_inventario: number;
}

interface Props {
    products: {
        data: Product[];
        links: any[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    stats: Stats;
    categories: Category[];
    filters: {
        search: string;
        categoria: string;
        stock_filter: string;
        sort_by: string;
        sort_direction: string;
    };
    error?: string;
}

const stockStatusColors = {
    normal: 'bg-green-100 text-green-800',
    bajo: 'bg-yellow-100 text-yellow-800',
    sin_stock: 'bg-red-100 text-red-800',
    exceso: 'bg-blue-100 text-blue-800',
};

const stockStatusLabels = {
    normal: 'Normal',
    bajo: 'Stock Bajo',
    sin_stock: 'Sin Stock',
    exceso: 'Exceso',
};

export default function Inventory({ products, stats, categories, filters, error }: Props) {
    const [search, setSearch] = useState(filters.search || '');
    const [selectedCategory, setSelectedCategory] = useState(filters.categoria || '');
    const [stockFilter, setStockFilter] = useState(filters.stock_filter || 'todos');
    const [showAdjustModal, setShowAdjustModal] = useState(false);
    const [selectedProduct, setSelectedProduct] = useState<Product | null>(null);
    const [adjustmentData, setAdjustmentData] = useState({
        cantidad: '',
        tipo_ajuste: 'entrada' as 'entrada' | 'salida' | 'ajuste',
        motivo: ''
    });

    if (error) {
        return (
            <AuthenticatedLayout>
                <Head title="Inventario" />
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
        router.get('/inventario', {
            search,
            categoria: selectedCategory,
            stock_filter: stockFilter,
            sort_by: filters.sort_by,
            sort_direction: filters.sort_direction,
        }, { preserveState: true });
    };

    const handleSort = (column: string) => {
        const direction = filters.sort_by === column && filters.sort_direction === 'asc' ? 'desc' : 'asc';
        router.get('/inventario', {
            ...filters,
            sort_by: column,
            sort_direction: direction,
        }, { preserveState: true });
    };

    const openAdjustModal = (product: Product) => {
        setSelectedProduct(product);
        setShowAdjustModal(true);
        setAdjustmentData({
            cantidad: '',
            tipo_ajuste: 'entrada',
            motivo: ''
        });
    };

    const handleAdjustStock = async () => {
        if (!selectedProduct || !adjustmentData.cantidad || !adjustmentData.motivo) {
            return;
        }

        try {
            await router.post('/inventario/adjust-stock', {
                producto_id: selectedProduct.id,
                cantidad: parseFloat(adjustmentData.cantidad),
                tipo_ajuste: adjustmentData.tipo_ajuste,
                motivo: adjustmentData.motivo,
            });
            
            setShowAdjustModal(false);
            setSelectedProduct(null);
            router.reload();
        } catch (error) {
            console.error('Error adjusting stock:', error);
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Inventario" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="mb-8">
                        <h1 className="text-3xl font-bold text-gray-900">Inventario</h1>
                        <p className="mt-2 text-gray-600">Gestión y control de stock de productos</p>
                    </div>

                    {/* Estadísticas */}
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <StatsCard
                            title="Total Productos"
                            value={stats.total_productos.toString()}
                            icon={ChartBarIcon}
                            trend="neutral"
                        />
                        <StatsCard
                            title="Stock Bajo"
                            value={stats.productos_stock_bajo.toString()}
                            icon={ExclamationTriangleIcon}
                            trend={stats.productos_stock_bajo > 0 ? "down" : "neutral"}
                            className={stats.productos_stock_bajo > 0 ? "border-yellow-200" : ""}
                        />
                        <StatsCard
                            title="Sin Stock"
                            value={stats.productos_sin_stock.toString()}
                            icon={ExclamationTriangleIcon}
                            trend={stats.productos_sin_stock > 0 ? "down" : "neutral"}
                            className={stats.productos_sin_stock > 0 ? "border-red-200" : ""}
                        />
                        <StatsCard
                            title="Valor Inventario"
                            value={`$${stats.valor_inventario.toLocaleString()}`}
                            icon={ChartBarIcon}
                            trend="neutral"
                        />
                    </div>

                    {/* Filtros */}
                    <Card className="mb-6">
                        <div className="flex flex-wrap gap-4 items-center">
                            <div className="flex-1 min-w-64">
                                <div className="relative">
                                    <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                    <input
                                        type="text"
                                        placeholder="Buscar productos..."
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
                                        className="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                            </div>
                            
                            <select
                                value={selectedCategory}
                                onChange={(e) => setSelectedCategory(e.target.value)}
                                className="border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Todas las categorías</option>
                                {categories.map((category) => (
                                    <option key={category.id} value={category.id}>
                                        {category.nombre}
                                    </option>
                                ))}
                            </select>
                            
                            <select
                                value={stockFilter}
                                onChange={(e) => setStockFilter(e.target.value)}
                                className="border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="todos">Todos los productos</option>
                                <option value="bajo">Stock bajo</option>
                                <option value="sin_stock">Sin stock</option>
                                <option value="exceso">Stock en exceso</option>
                            </select>
                            
                            <Button onClick={handleSearch}>
                                Filtrar
                            </Button>

                            <Link href="/inventario/movements">
                                <Button variant="outline">
                                    <ClockIcon className="h-4 w-4 mr-2" />
                                    Ver Movimientos
                                </Button>
                            </Link>
                        </div>
                    </Card>

                    {/* Tabla de productos */}
                    <Card>
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th 
                                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            onClick={() => handleSort('nombre')}
                                        >
                                            Producto
                                            {filters.sort_by === 'nombre' && (
                                                <span className="ml-1">
                                                    {filters.sort_direction === 'asc' ? '↑' : '↓'}
                                                </span>
                                            )}
                                        </th>
                                        <th 
                                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            onClick={() => handleSort('categoria')}
                                        >
                                            Categoría
                                        </th>
                                        <th 
                                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            onClick={() => handleSort('stock')}
                                        >
                                            Stock
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Precios
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Valor
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {products.data.map((product) => (
                                        <tr key={product.id} className="hover:bg-gray-50">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div>
                                                    <div className="text-sm font-medium text-gray-900">
                                                        {product.nombre}
                                                    </div>
                                                    <div className="text-sm text-gray-500">
                                                        {product.codigo_interno}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {product.categoria}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm text-gray-900">
                                                    <div>Actual: <span className="font-medium">{product.stock_actual}</span></div>
                                                    {product.stock_reservado > 0 && (
                                                        <div className="text-xs text-gray-500">
                                                            Disponible: {product.stock_disponible}
                                                        </div>
                                                    )}
                                                    <div className="text-xs text-gray-500">
                                                        Mín: {product.stock_minimo} | Máx: {product.stock_maximo}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div>Costo: ${product.precio_costo}</div>
                                                <div>Venta: ${product.precio_venta}</div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${stockStatusColors[product.estado_stock]}`}>
                                                    {stockStatusLabels[product.estado_stock]}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ${product.valor_inventario.toLocaleString()}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => openAdjustModal(product)}
                                                >
                                                    <AdjustmentsHorizontalIcon className="h-4 w-4 mr-1" />
                                                    Ajustar
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Paginación */}
                        {products.last_page > 1 && (
                            <div className="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                                <div className="flex items-center justify-between">
                                    <div className="text-sm text-gray-700">
                                        Mostrando {((products.current_page - 1) * products.per_page) + 1} a {Math.min(products.current_page * products.per_page, products.total)} de {products.total} productos
                                    </div>
                                    <div className="flex space-x-2">
                                        {products.links.map((link, index) => (
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

                    {/* Modal de ajuste de stock */}
                    {showAdjustModal && selectedProduct && (
                        <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                            <div className="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                <div className="mt-3">
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">
                                        Ajustar Stock: {selectedProduct.nombre}
                                    </h3>
                                    <div className="mb-4 text-sm text-gray-600">
                                        Stock actual: {selectedProduct.stock_actual}
                                    </div>
                                    
                                    <div className="space-y-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Tipo de ajuste
                                            </label>
                                            <select
                                                value={adjustmentData.tipo_ajuste}
                                                onChange={(e) => setAdjustmentData({
                                                    ...adjustmentData, 
                                                    tipo_ajuste: e.target.value as 'entrada' | 'salida' | 'ajuste'
                                                })}
                                                className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                            >
                                                <option value="entrada">Entrada (+)</option>
                                                <option value="salida">Salida (-)</option>
                                                <option value="ajuste">Ajuste (=)</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Cantidad
                                            </label>
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                value={adjustmentData.cantidad}
                                                onChange={(e) => setAdjustmentData({...adjustmentData, cantidad: e.target.value})}
                                                className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                                placeholder="0.00"
                                            />
                                        </div>
                                        
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Motivo
                                            </label>
                                            <input
                                                type="text"
                                                value={adjustmentData.motivo}
                                                onChange={(e) => setAdjustmentData({...adjustmentData, motivo: e.target.value})}
                                                className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                                placeholder="Describe el motivo del ajuste"
                                            />
                                        </div>
                                    </div>
                                    
                                    <div className="flex space-x-3 mt-6">
                                        <Button
                                            onClick={handleAdjustStock}
                                            disabled={!adjustmentData.cantidad || !adjustmentData.motivo}
                                            className="flex-1"
                                        >
                                            Confirmar Ajuste
                                        </Button>
                                        <Button
                                            variant="outline"
                                            onClick={() => setShowAdjustModal(false)}
                                            className="flex-1"
                                        >
                                            Cancelar
                                        </Button>
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
