import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Card from '@/Components/Card';
import Button from '@/Components/Button';
import { 
    PlusIcon,
    TrashIcon,
    MagnifyingGlassIcon,
    BuildingOfficeIcon
} from '@heroicons/react/24/outline';

interface Supplier {
    id: number;
    nombre: string;
    ruc: string;
    telefono?: string;
    email?: string;
}

interface Product {
    id: number;
    nombre: string;
    codigo_interno: string;
    precio_costo: number;
    stock_actual: number;
}

interface PurchaseItem {
    producto_id: number;
    producto_nombre: string;
    cantidad: number;
    precio_unitario: number;
    subtotal: number;
}

interface CreatePurchaseProps {
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
        };
    };
    suppliers: Supplier[];
    products: Product[];
    errors: any;
}

export default function CreatePurchase({ auth, suppliers, products, errors }: CreatePurchaseProps) {
    const [formData, setFormData] = useState({
        proveedor_id: '',
        fecha_compra: new Date().toISOString().split('T')[0],
        fecha_entrega: '',
        notas: ''
    });
    
    const [items, setItems] = useState<PurchaseItem[]>([]);
    const [selectedProductId, setSelectedProductId] = useState('');
    const [productSearch, setProductSearch] = useState('');
    const [itemQuantity, setItemQuantity] = useState('');
    const [itemPrice, setItemPrice] = useState('');

    const filteredProducts = products.filter(product =>
        product.nombre.toLowerCase().includes(productSearch.toLowerCase()) ||
        product.codigo_interno.toLowerCase().includes(productSearch.toLowerCase())
    );

    const selectedProduct = products.find(p => p.id.toString() === selectedProductId);

    const addItem = () => {
        if (!selectedProductId || !itemQuantity || !itemPrice) {
            alert('Por favor completa todos los campos del producto');
            return;
        }

        const product = products.find(p => p.id.toString() === selectedProductId);
        if (!product) return;

        const quantity = parseFloat(itemQuantity);
        const price = parseFloat(itemPrice);
        const subtotal = quantity * price;

        const newItem: PurchaseItem = {
            producto_id: product.id,
            producto_nombre: product.nombre,
            cantidad: quantity,
            precio_unitario: price,
            subtotal: subtotal
        };

        setItems([...items, newItem]);
        setSelectedProductId('');
        setProductSearch('');
        setItemQuantity('');
        setItemPrice('');
    };

    const removeItem = (index: number) => {
        const newItems = items.filter((_, i) => i !== index);
        setItems(newItems);
    };

    const calculateTotals = () => {
        const subtotal = items.reduce((sum, item) => sum + item.subtotal, 0);
        const impuestos = subtotal * 0.18; // IGV 18%
        const total = subtotal + impuestos;
        
        return { subtotal, impuestos, total };
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        if (!formData.proveedor_id) {
            alert('Por favor selecciona un proveedor');
            return;
        }
        
        if (items.length === 0) {
            alert('Por favor agrega al menos un producto');
            return;
        }

        const { subtotal, impuestos, total } = calculateTotals();

        const purchaseData = {
            proveedor_id: formData.proveedor_id,
            fecha_compra: formData.fecha_compra,
            fecha_entrega: formData.fecha_entrega,
            notas: formData.notas,
            items: JSON.stringify(items),
            subtotal: subtotal.toString(),
            impuestos: impuestos.toString(),
            total: total.toString()
        };

        router.post('/purchases', purchaseData, {
            onSuccess: () => {
                // Redirigir a la lista de compras
            },
            onError: (errors) => {
                console.error('Error creating purchase:', errors);
            }
        });
    };

    const { subtotal, impuestos, total } = calculateTotals();

    return (
        <AuthenticatedLayout
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                            🛒 Nueva Orden de Compra
                        </h2>
                        <p className="text-gray-600 text-sm mt-1">
                            Crear una nueva orden de compra a proveedor
                        </p>
                    </div>
                </div>
            }
        >
            <Head title="Nueva Compra" />
            
            <div className="py-6">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    
                    <form onSubmit={handleSubmit}>
                        {/* Información General */}
                        <Card>
                            <div className="p-6">
                                <h3 className="text-lg font-medium text-gray-900 mb-4">Información de la Compra</h3>
                                
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Proveedor *
                                        </label>
                                        <select
                                            value={formData.proveedor_id}
                                            onChange={(e) => setFormData({...formData, proveedor_id: e.target.value})}
                                            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                            required
                                        >
                                            <option value="">Seleccionar proveedor</option>
                                            {suppliers.map((supplier) => (
                                                <option key={supplier.id} value={supplier.id}>
                                                    {supplier.nombre} - {supplier.ruc}
                                                </option>
                                            ))}
                                        </select>
                                        {errors.proveedor_id && (
                                            <p className="text-red-500 text-sm mt-1">{errors.proveedor_id}</p>
                                        )}
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Fecha de Compra *
                                        </label>
                                        <input
                                            type="date"
                                            value={formData.fecha_compra}
                                            onChange={(e) => setFormData({...formData, fecha_compra: e.target.value})}
                                            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Fecha de Entrega Estimada
                                        </label>
                                        <input
                                            type="date"
                                            value={formData.fecha_entrega}
                                            onChange={(e) => setFormData({...formData, fecha_entrega: e.target.value})}
                                            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Notas
                                        </label>
                                        <textarea
                                            value={formData.notas}
                                            onChange={(e) => setFormData({...formData, notas: e.target.value})}
                                            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                            rows={3}
                                            placeholder="Notas adicionales sobre la compra..."
                                        />
                                    </div>
                                </div>
                            </div>
                        </Card>

                        {/* Productos */}
                        <Card>
                            <div className="p-6">
                                <h3 className="text-lg font-medium text-gray-900 mb-4">Productos a Comprar</h3>
                                
                                {/* Agregar Producto */}
                                <div className="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Buscar Producto
                                        </label>
                                        <div className="relative">
                                            <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                                            <input
                                                type="text"
                                                value={productSearch}
                                                onChange={(e) => setProductSearch(e.target.value)}
                                                placeholder="Buscar producto..."
                                                className="pl-10 w-full border border-gray-300 rounded-md px-3 py-2"
                                            />
                                        </div>
                                        {productSearch && (
                                            <div className="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                                {filteredProducts.map((product) => (
                                                    <div
                                                        key={product.id}
                                                        onClick={() => {
                                                            setSelectedProductId(product.id.toString());
                                                            setProductSearch(product.nombre);
                                                            setItemPrice(product.precio_costo.toString());
                                                        }}
                                                        className="p-3 hover:bg-gray-100 cursor-pointer border-b"
                                                    >
                                                        <div className="font-medium">{product.nombre}</div>
                                                        <div className="text-sm text-gray-500">
                                                            Código: {product.codigo_interno} | Stock: {product.stock_actual}
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        )}
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Cantidad
                                        </label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            value={itemQuantity}
                                            onChange={(e) => setItemQuantity(e.target.value)}
                                            placeholder="0"
                                            className="w-full border border-gray-300 rounded-md px-3 py-2"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Precio Unitario
                                        </label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            value={itemPrice}
                                            onChange={(e) => setItemPrice(e.target.value)}
                                            placeholder="0.00"
                                            className="w-full border border-gray-300 rounded-md px-3 py-2"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Subtotal
                                        </label>
                                        <input
                                            type="text"
                                            value={itemQuantity && itemPrice ? `S/. ${(parseFloat(itemQuantity) * parseFloat(itemPrice)).toFixed(2)}` : 'S/. 0.00'}
                                            readOnly
                                            className="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100"
                                        />
                                    </div>

                                    <div className="flex items-end">
                                        <Button
                                            type="button"
                                            onClick={addItem}
                                            className="w-full"
                                            disabled={!selectedProductId || !itemQuantity || !itemPrice}
                                        >
                                            <PlusIcon className="w-4 h-4 mr-2" />
                                            Agregar
                                        </Button>
                                    </div>
                                </div>

                                {/* Lista de Productos */}
                                {items.length > 0 && (
                                    <div className="overflow-x-auto">
                                        <table className="min-w-full divide-y divide-gray-200">
                                            <thead className="bg-gray-50">
                                                <tr>
                                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Producto
                                                    </th>
                                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Cantidad
                                                    </th>
                                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Precio Unit.
                                                    </th>
                                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Subtotal
                                                    </th>
                                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Acciones
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody className="bg-white divide-y divide-gray-200">
                                                {items.map((item, index) => (
                                                    <tr key={index}>
                                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            {item.producto_nombre}
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {item.cantidad}
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            S/. {item.precio_unitario.toFixed(2)}
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            S/. {item.subtotal.toFixed(2)}
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                            <Button
                                                                type="button"
                                                                variant="outline"
                                                                size="sm"
                                                                onClick={() => removeItem(index)}
                                                                className="text-red-600 border-red-600 hover:bg-red-50"
                                                            >
                                                                <TrashIcon className="w-4 h-4" />
                                                            </Button>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                )}
                            </div>
                        </Card>

                        {/* Totales y Acciones */}
                        <Card>
                            <div className="p-6">
                                <div className="flex justify-between items-center">
                                    <div className="space-y-2">
                                        <h3 className="text-lg font-medium text-gray-900">Resumen de la Compra</h3>
                                        <p className="text-sm text-gray-600">
                                            {items.length} producto{items.length !== 1 ? 's' : ''} agregado{items.length !== 1 ? 's' : ''}
                                        </p>
                                    </div>
                                    
                                    <div className="text-right space-y-1">
                                        <div className="text-sm">
                                            <span className="font-medium">Subtotal:</span> S/. {subtotal.toFixed(2)}
                                        </div>
                                        <div className="text-sm">
                                            <span className="font-medium">IGV (18%):</span> S/. {impuestos.toFixed(2)}
                                        </div>
                                        <div className="text-lg font-bold">
                                            <span className="font-medium">Total:</span> S/. {total.toFixed(2)}
                                        </div>
                                    </div>
                                </div>
                                
                                <div className="flex justify-end space-x-4 mt-6">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={() => router.get('/purchases')}
                                    >
                                        Cancelar
                                    </Button>
                                    <Button
                                        type="submit"
                                        disabled={!formData.proveedor_id || items.length === 0}
                                    >
                                        Crear Orden de Compra
                                    </Button>
                                </div>
                            </div>
                        </Card>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}