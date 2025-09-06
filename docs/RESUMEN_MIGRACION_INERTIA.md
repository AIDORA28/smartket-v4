# 🚀 MIGRACIÓN INERTIA.JS - Plan de Implementación

## 🎯 OBJETIVO DE LA MIGRACIÓN

**Transformar SmartKet de Livewire a React + Inertia.js** para obtener:
- ⚡ **Performance superior** (SPA-like navigation)
- 💼 **UX empresarial** (sin recargas de página)
- 📱 **Mobile experience** mejorada
- 🔄 **Real-time updates** más fluidos
- 🎨 **UI moderna** con componentes reutilizables

## 📋 PLAN DE MIGRACIÓN GRADUAL

### **FASE 1: Setup Base (1 semana)**
```bash
# 1. Instalar dependencias
npm install @inertiajs/react @inertiajs/inertia react react-dom
npm install -D @types/react @types/react-dom typescript vite

# 2. Configurar Vite + TypeScript
# 3. Setup Inertia middleware
# 4. Crear layout base React
```

### **FASE 2: Módulos Core (3 semanas)**
```
Semana 1: Dashboard + Navegación
Semana 2: Productos (CRUD completo)
Semana 3: POS (Punto de Venta)
```

### **FASE 3: Módulos Avanzados (2 semanas)**
```
Semana 1: Inventario + Stock
Semana 2: Clientes + Reportes
```

### **FASE 4: Optimizaciones (1 semana)**
```
- PWA Service Worker
- Lazy loading components
- Performance tuning
- Testing completo
```

## 🔧 CONFIGURACIÓN TÉCNICA

### **1. Instalación Dependencias:**
```json
{
  "dependencies": {
    "@inertiajs/react": "^1.0.0",
    "react": "^18.2.0",
    "react-dom": "^18.2.0",
    "@heroicons/react": "^2.0.18",
    "@headlessui/react": "^1.7.17",
    "clsx": "^2.0.0",
    "date-fns": "^2.30.0"
  },
  "devDependencies": {
    "@types/react": "^18.2.37",
    "@types/react-dom": "^18.2.15",
    "@vitejs/plugin-react": "^4.1.1",
    "typescript": "^5.2.2",
    "vite": "^5.0.0"
  }
}
```

### **2. Laravel Backend (Sin cambios):**
```php
// app/Http/Middleware/HandleInertiaRequests.php
class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
                'empresa' => $request->user()?->empresa,
            ],
            'flash' => [
                'message' => session('message'),
                'error' => session('error'),
            ],
            'current_tenant' => session('tenant_id'),
        ]);
    }
}
```

### **3. Vite Configuration:**
```js
// vite.config.js
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.tsx',
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
})
```

## ⚛️ ESTRUCTURA REACT

### **Layout Principal:**
```tsx
// resources/js/Layouts/AppLayout.tsx
import React from 'react'
import { Head } from '@inertiajs/react'
import Sidebar from '@/Components/Sidebar'
import TopBar from '@/Components/TopBar'

interface Props {
  children: React.ReactNode
  title: string
}

export default function AppLayout({ children, title }: Props) {
  return (
    <div className="min-h-screen bg-gray-50">
      <Head title={title} />
      <div className="flex">
        <Sidebar />
        <div className="flex-1">
          <TopBar />
          <main className="p-6">
            {children}
          </main>
        </div>
      </div>
    </div>
  )
}
```

### **Componentes Reutilizables:**
```tsx
// resources/js/Components/ui/Button.tsx
interface ButtonProps {
  variant?: 'primary' | 'secondary' | 'danger'
  size?: 'sm' | 'md' | 'lg'
  loading?: boolean
  children: React.ReactNode
  onClick?: () => void
}

export function Button({ 
  variant = 'primary', 
  size = 'md', 
  loading = false,
  children, 
  ...props 
}: ButtonProps) {
  const baseClasses = 'inline-flex items-center justify-center rounded-md font-medium transition-colors'
  
  const variants = {
    primary: 'bg-blue-600 text-white hover:bg-blue-700',
    secondary: 'bg-gray-200 text-gray-900 hover:bg-gray-300',
    danger: 'bg-red-600 text-white hover:bg-red-700'
  }
  
  const sizes = {
    sm: 'px-3 py-2 text-sm',
    md: 'px-4 py-2 text-base',
    lg: 'px-6 py-3 text-lg'
  }
  
  return (
    <button
      className={clsx(baseClasses, variants[variant], sizes[size])}
      disabled={loading}
      {...props}
    >
      {loading && <Spinner className="mr-2" />}
      {children}
    </button>
  )
}
```

## 🛒 PÁGINAS PRINCIPALES

### **1. Dashboard:**
```tsx
// resources/js/Pages/Dashboard.tsx
import { Head } from '@inertiajs/react'
import AppLayout from '@/Layouts/AppLayout'
import StatsCard from '@/Components/StatsCard'

interface Props {
  stats: {
    ventas_hoy: number
    productos_stock_bajo: number
    clientes_nuevos: number
    ingresos_mes: number
  }
}

export default function Dashboard({ stats }: Props) {
  return (
    <AppLayout title="Dashboard">
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatsCard 
          title="Ventas Hoy" 
          value={stats.ventas_hoy} 
          icon="ShoppingCartIcon"
          color="blue" 
        />
        <StatsCard 
          title="Stock Bajo" 
          value={stats.productos_stock_bajo} 
          icon="ExclamationTriangleIcon"
          color="red" 
        />
        <StatsCard 
          title="Clientes Nuevos" 
          value={stats.clientes_nuevos} 
          icon="UserGroupIcon"
          color="green" 
        />
        <StatsCard 
          title="Ingresos del Mes" 
          value={`S/. ${stats.ingresos_mes}`} 
          icon="BanknotesIcon"
          color="purple" 
        />
      </div>
    </AppLayout>
  )
}
```

### **2. Lista de Productos:**
```tsx
// resources/js/Pages/Productos/Index.tsx
import { useState } from 'react'
import { Head, Link, router } from '@inertiajs/react'
import AppLayout from '@/Layouts/AppLayout'
import { Button } from '@/Components/ui/Button'
import ProductoTable from '@/Components/ProductoTable'

interface Props {
  productos: {
    data: Producto[]
    links: any[]
    meta: any
  }
  filters: {
    search?: string
    categoria?: string
  }
}

export default function ProductosIndex({ productos, filters }: Props) {
  const [search, setSearch] = useState(filters.search || '')
  
  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault()
    router.get('/productos', { search }, { 
      preserveState: true,
      replace: true 
    })
  }

  return (
    <AppLayout title="Productos">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold">Gestión de Productos</h1>
        <Link href="/productos/create">
          <Button>Nuevo Producto</Button>
        </Link>
      </div>
      
      <div className="bg-white rounded-lg shadow">
        <div className="p-6 border-b">
          <form onSubmit={handleSearch} className="flex gap-4">
            <input
              type="search"
              placeholder="Buscar productos..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="flex-1 rounded-md border-gray-300"
            />
            <Button type="submit">Buscar</Button>
          </form>
        </div>
        
        <ProductoTable 
          productos={productos.data}
          links={productos.links}
        />
      </div>
    </AppLayout>
  )
}
```

### **3. POS (Punto de Venta):**
```tsx
// resources/js/Pages/POS/Index.tsx
import { useState } from 'react'
import AppLayout from '@/Layouts/AppLayout'
import ProductGrid from '@/Components/POS/ProductGrid'
import CarritoSidebar from '@/Components/POS/CarritoSidebar'

export default function POS() {
  const [carrito, setCarrito] = useState<CarritoItem[]>([])
  const [cliente, setCliente] = useState<Cliente | null>(null)
  
  const agregarProducto = (producto: Producto) => {
    setCarrito(prev => {
      const existe = prev.find(item => item.producto_id === producto.id)
      
      if (existe) {
        return prev.map(item =>
          item.producto_id === producto.id
            ? { ...item, cantidad: item.cantidad + 1 }
            : item
        )
      }
      
      return [...prev, {
        producto_id: producto.id,
        nombre: producto.nombre,
        precio: producto.precio_venta,
        cantidad: 1
      }]
    })
  }
  
  return (
    <AppLayout title="Punto de Venta">
      <div className="flex gap-6 h-[calc(100vh-200px)]">
        <div className="flex-1">
          <ProductGrid onProductSelect={agregarProducto} />
        </div>
        
        <div className="w-96">
          <CarritoSidebar
            items={carrito}
            onUpdateItem={setCarrito}
            cliente={cliente}
            onSelectCliente={setCliente}
          />
        </div>
      </div>
    </AppLayout>
  )
}
```

## 🔄 ESTADO GLOBAL Y HOOKS

### **Context para Carrito POS:**
```tsx
// resources/js/Contexts/CarritoContext.tsx
import { createContext, useContext, useReducer } from 'react'

interface CarritoState {
  items: CarritoItem[]
  total: number
  cliente: Cliente | null
}

type CarritoAction = 
  | { type: 'AGREGAR_PRODUCTO'; payload: Producto }
  | { type: 'ACTUALIZAR_CANTIDAD'; payload: { id: number; cantidad: number } }
  | { type: 'REMOVER_ITEM'; payload: number }
  | { type: 'LIMPIAR_CARRITO' }
  | { type: 'SET_CLIENTE'; payload: Cliente }

const CarritoContext = createContext<{
  state: CarritoState
  dispatch: React.Dispatch<CarritoAction>
} | null>(null)

export function useCarrito() {
  const context = useContext(CarritoContext)
  if (!context) throw new Error('useCarrito debe usarse dentro de CarritoProvider')
  return context
}
```

### **Custom Hooks:**
```tsx
// resources/js/Hooks/useDebounce.ts
import { useState, useEffect } from 'react'

export function useDebounce<T>(value: T, delay: number): T {
  const [debouncedValue, setDebouncedValue] = useState<T>(value)

  useEffect(() => {
    const handler = setTimeout(() => {
      setDebouncedValue(value)
    }, delay)

    return () => {
      clearTimeout(handler)
    }
  }, [value, delay])

  return debouncedValue
}

// resources/js/Hooks/useProducts.ts
import { useState, useEffect } from 'react'
import { router } from '@inertiajs/react'

export function useProducts() {
  const [loading, setLoading] = useState(false)
  const [productos, setProductos] = useState<Producto[]>([])
  
  const buscarProductos = async (query: string) => {
    setLoading(true)
    try {
      const response = await fetch(`/api/productos/search?q=${query}`)
      const data = await response.json()
      setProductos(data.productos)
    } finally {
      setLoading(false)
    }
  }
  
  return { productos, loading, buscarProductos }
}
```

## ⚡ OPTIMIZACIONES PERFORMANCE

### **Code Splitting:**
```tsx
// resources/js/app.tsx
import { lazy } from 'react'

// Lazy loading de páginas
const Dashboard = lazy(() => import('./Pages/Dashboard'))
const ProductosIndex = lazy(() => import('./Pages/Productos/Index'))
const POS = lazy(() => import('./Pages/POS/Index'))

// Componente de carga
function PageLoader() {
  return (
    <div className="flex items-center justify-center min-h-screen">
      <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
    </div>
  )
}
```

### **Service Worker (PWA):**
```js
// public/sw.js
const CACHE_NAME = 'smartket-v1'
const urlsToCache = [
  '/',
  '/build/assets/app.css',
  '/build/assets/app.js',
  '/img/logo.svg'
]

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(urlsToCache))
  )
})

self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request)
      .then((response) => response || fetch(event.request))
  )
})
```

## 🧪 TESTING

### **Component Testing:**
```tsx
// tests/Components/Button.test.tsx
import { render, screen, fireEvent } from '@testing-library/react'
import { Button } from '@/Components/ui/Button'

describe('Button', () => {
  test('renders with correct text', () => {
    render(<Button>Click me</Button>)
    expect(screen.getByText('Click me')).toBeInTheDocument()
  })

  test('calls onClick when clicked', () => {
    const handleClick = jest.fn()
    render(<Button onClick={handleClick}>Click me</Button>)
    
    fireEvent.click(screen.getByText('Click me'))
    expect(handleClick).toHaveBeenCalledTimes(1)
  })
})
```

---

**Migración completa a React + Inertia.js** manteniendo el **backend Laravel** sin cambios, con **performance superior** y **UX empresarial**.
