// Global Type Definitions for SmartKet v4
export interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at?: string;
  empresa_id: number;
  sucursal_id?: number;
  rol_principal: string;
  activo: boolean;
  last_login_at?: string;
  created_at?: string;
  updated_at?: string;
}

export interface Tenant {
  id: number;
  name: string;
  subdomain: string;
  database: string;
  settings?: Record<string, any>;
}

export interface Role {
  id: number;
  name: string;
  guard_name: string;
  permissions?: Permission[];
}

export interface Permission {
  id: number;
  name: string;
  guard_name: string;
}

export interface Product {
  id: number;
  tenant_id: number;
  name: string;
  description?: string;
  sku: string;
  price: number;
  cost: number;
  stock: number;
  min_stock: number;
  category_id?: number;
  category?: Category;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface Category {
  id: number;
  tenant_id: number;
  name: string;
  description?: string;
  is_active: boolean;
  products_count?: number;
}

export interface Sale {
  id: number;
  tenant_id: number;
  user_id: number;
  customer_id?: number;
  total: number;
  discount: number;
  tax: number;
  status: 'pending' | 'completed' | 'cancelled';
  payment_method: string;
  notes?: string;
  items: SaleItem[];
  created_at: string;
  updated_at: string;
}

export interface SaleItem {
  id: number;
  sale_id: number;
  product_id: number;
  product: Product;
  quantity: number;
  unit_price: number;
  total_price: number;
}

export interface PaginatedData<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from?: number;
  to?: number;
  links: {
    first?: string;
    last?: string;
    prev?: string;
    next?: string;
  };
}

export interface FlashMessage {
  type: 'success' | 'error' | 'warning' | 'info';
  message: string;
}

export interface PageProps {
  auth: {
    user: User;
    tenant: Tenant;
  };
  flash?: FlashMessage;
  errors?: Record<string, string>;
  [key: string]: any;
}
