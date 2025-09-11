// Tipos centralizados para el Core Dashboard Multi-Tenant
export interface User {
  id: number;
  name: string;
  email: string;
  empresa_id?: number;
  sucursal_id?: number;
  rol_principal: string;
  activo?: boolean;
}

export interface Empresa {
  id: number;
  nombre: string;
  nombre_empresa: string;
  logo?: string;
  ruc?: string;
  direccion?: string;
  telefono?: string;
  plan?: {
    id: number;
    nombre: string;
  };
}

export interface Sucursal {
  id: number;
  nombre: string;
  direccion?: string;
  telefono?: string;
  empresa_id: number;
}

export interface MultiTenantContext {
  empresa: Empresa | null;
  sucursal: Sucursal | null;
  empresasDisponibles: Empresa[];
  sucursalesDisponibles: Sucursal[];
}

export interface CoreDashboardProps {
  auth: {
    user: User;
  };
  empresa: Empresa;
  sucursal: Sucursal;
  empresas_disponibles: Empresa[];
  sucursales_disponibles: Sucursal[];
  stats: {
    ventasHoy: number;
    productosStock: number;
    clientesActivos: number;
    facturacionMensual: number;
  };
  features: {
    pos: boolean;
    inventario_avanzado: boolean;
    reportes: boolean;
    facturacion_electronica: boolean;
  };
  flash?: {
    success?: string;
    error?: string;
    warning?: string;
    message?: string;
  };
}

export interface CompanySelectorProps {
  currentEmpresa: Empresa | null;
  empresasDisponibles: Empresa[];
  onEmpresaChange: (empresaId: number) => void;
  isLoading?: boolean;
}

export interface BranchSelectorProps {
  currentSucursal: Sucursal | null;
  sucursalesDisponibles: Sucursal[];
  onSucursalChange: (sucursalId: number) => void;
  isLoading?: boolean;
}

export interface ContextIndicatorProps {
  empresa: Empresa | null;
  sucursal: Sucursal | null;
  planInfo?: string;
}

export interface MultiTenantHeaderProps {
  user: User;
  empresa: Empresa | null;
  sucursal: Sucursal | null;
  empresasDisponibles: Empresa[];
  sucursalesDisponibles: Sucursal[];
  onContextChange?: (empresaId: number, sucursalId: number) => void;
}

export interface NavigationSubModule {
  name: string;
  href: string;
  icon?: React.ComponentType<any>;
  current: boolean;
  roles: string[];
}

export interface NavigationModule {
  name: string;
  href: string;
  icon: React.ComponentType<any>;
  current: boolean;
  roles: string[];
  badge?: string;
  emoji?: string;
  locked?: boolean;
  accessType?: string;
  subModules?: NavigationSubModule[];
  expandable?: boolean;
}

export interface AuthenticatedLayoutProps {
  children: React.ReactNode;
  header?: React.ReactNode;
  title?: string;
}

// User Management Types - Fase 2
export interface UserFormData {
  id?: number;
  name: string;
  email: string;
  password?: string;
  password_confirmation?: string;
  rol_principal: string;
  empresa_id?: number;
  sucursal_id?: number;
  activo: boolean;
}

export interface UserListItem {
  id: number;
  name: string;
  email: string;
  rol_principal: string;
  empresa?: {
    id: number;
    nombre: string;
  };
  sucursal?: {
    id: number;
    nombre: string;
  };
  activo: boolean;
  created_at: string;
  last_login?: string;
}

export interface Role {
  key: string;              // ID único (usado como identificador principal)
  name: string;             // Nombre interno (mismo que key para compatibilidad)
  display_name: string;     // Nombre para mostrar al usuario
  description: string;      // Descripción del rol
  isPremium?: boolean;      // Si requiere plan premium
  permissions: string[];    // Lista de permisos
  level: number;           // Nivel jerárquico (1=mayor, 5=menor)
}

export interface Permission {
  id: string;
  name: string;
  display_name: string;
  category: string;
  description: string;
}

export interface UserIndexProps {
  users: {
    data: UserListItem[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  filters: {
    search?: string;
    role?: string;
    empresa?: string;
    status?: string;
  };
  roles: Role[];
  empresas: Empresa[];
}

export interface UserFormProps {
  user?: UserListItem;
  roles: Role[];
  empresas: Empresa[];
  sucursales: Sucursal[];
  isEditing?: boolean;
}

export interface RoleSelectorProps {
  selectedRole: string;
  roles: Role[];
  onRoleChange: (role: string) => void;
  disabled?: boolean;
}

export interface PermissionMatrixProps {
  selectedRole: string;
  permissions: Permission[];
  rolePermissions: string[];
  onPermissionChange: (permission: string, granted: boolean) => void;
  readOnly?: boolean;
}

// Company & Branch Management Types - Fase 3
export interface EmpresaSettings {
  id?: number;
  empresa_id: number;
  configuracion_notificaciones?: {
    email_enabled?: boolean;
    sms_enabled?: boolean;
    push_enabled?: boolean;
    daily_reports?: boolean;
    weekly_reports?: boolean;
  };
  configuracion_facturacion?: {
    serie_defecto?: string;
    numeracion_automatica?: boolean;
    incluir_igv?: boolean;
    formato_fecha?: string;
    logo_facturas?: boolean;
  };
  configuracion_inventario?: {
    alerta_stock_minimo?: boolean;
    umbral_stock_bajo?: number;
    permitir_stock_negativo?: boolean;
    valoracion_inventario?: 'fifo' | 'lifo' | 'promedio';
  };
  configuracion_ventas?: {
    descuentos_automaticos?: boolean;
    promociones_activas?: boolean;
    limite_descuento_empleado?: number;
    requiere_supervisor_descuentos?: boolean;
  };
  configuracion_backup?: {
    frecuencia?: 'diario' | 'semanal' | 'mensual';
    retencion_dias?: number;
    incluir_imagenes?: boolean;
    backup_automatico?: boolean;
  };
  zona_horaria_predeterminada: string;
  idioma_predeterminado: string;
  moneda_predeterminada: string;
  configuracion_ui?: {
    tema_oscuro?: boolean;
    sidebar_colapsado?: boolean;
    mostrar_ayuda?: boolean;
    animaciones_activas?: boolean;
  };
  configuracion_seguridad?: {
    sesion_timeout?: number;
    max_intentos_login?: number;
    requiere_2fa?: boolean;
    politica_passwords?: {
      min_length?: number;
      require_numbers?: boolean;
      require_symbols?: boolean;
      require_uppercase?: boolean;
    };
  };
  created_at?: string;
  updated_at?: string;
}

export interface OrganizationBranding {
  id?: number;
  empresa_id: number;
  primary_color: string;
  secondary_color: string;
  accent_color: string;
  logo_url?: string;
  favicon_url?: string;
  background_image_url?: string;
  typography_config?: {
    font_family?: string;
    heading_font?: string;
    font_size_base?: number;
    line_height?: number;
  };
  ui_theme: 'light' | 'dark' | 'auto';
  email_template_config?: {
    header_image?: string;
    footer_text?: string;
    social_links?: boolean;
    custom_css?: string;
  };
  invoice_template_config?: {
    logo_position?: 'left' | 'center' | 'right';
    color_scheme?: string;
    footer_message?: string;
    terms_conditions?: string;
  };
  social_media_links?: {
    facebook?: string;
    instagram?: string;
    twitter?: string;
    linkedin?: string;
    whatsapp?: string;
    website?: string;
  };
  custom_css?: string;
  brand_guidelines?: {
    logo_usage?: string;
    color_usage?: string;
    typography_rules?: string;
    do_dont_list?: string[];
  };
  created_at?: string;
  updated_at?: string;
}

export interface EmpresaAnalytics {
  id?: number;
  empresa_id: number;
  total_ventas_mes: number;
  total_ventas_año: number;
  margen_promedio: number;
  crecimiento_mensual: number;
  total_clientes: number;
  clientes_activos_mes: number;
  total_productos: number;
  productos_vendidos_mes: number;
  total_sucursales: number;
  sucursal_mas_vendedora_id?: number;
  productos_mas_vendidos?: {
    id: number;
    nombre: string;
    cantidad_vendida: number;
    ingresos: number;
  }[];
  categorias_mas_vendidas?: {
    id: number;
    nombre: string;
    cantidad_vendida: number;
    ingresos: number;
  }[];
  rating_promedio?: number;
  total_reviews: number;
  alertas_configuradas?: {
    stock_bajo?: boolean;
    ventas_diarias?: boolean;
    nuevos_clientes?: boolean;
  };
  reportes_automaticos?: {
    diario?: boolean;
    semanal?: boolean;
    mensual?: boolean;
    recipients?: string[];
  };
  ultima_actualizacion?: string;
  created_at?: string;
  updated_at?: string;
}

export interface SucursalSettings {
  id?: number;
  sucursal_id: number;
  horarios_atencion?: {
    lunes?: { apertura: string; cierre: string; activo: boolean };
    martes?: { apertura: string; cierre: string; activo: boolean };
    miercoles?: { apertura: string; cierre: string; activo: boolean };
    jueves?: { apertura: string; cierre: string; activo: boolean };
    viernes?: { apertura: string; cierre: string; activo: boolean };
    sabado?: { apertura: string; cierre: string; activo: boolean };
    domingo?: { apertura: string; cierre: string; activo: boolean };
  };
  configuracion_pos?: {
    impresora_default?: string;
    formato_ticket?: 'termico' | 'laser';
    mostrar_logo?: boolean;
    mensaje_ticket?: string;
  };
  configuracion_inventario?: {
    alerta_stock_sucursal?: boolean;
    umbral_personalizado?: number;
    permitir_transferencias?: boolean;
  };
  limite_usuarios: number;
  roles_permitidos?: string[];
  permite_ventas_online: boolean;
  permite_delivery: boolean;
  radio_delivery_km?: number;
  costo_delivery?: number;
  configuracion_reportes?: {
    reporte_diario?: boolean;
    reporte_semanal?: boolean;
    email_reportes?: string[];
  };
  reporte_diario_automatico: boolean;
  configuracion_cajas?: {
    cajas_activas?: number;
    monto_inicial_caja?: number;
    arqueo_automatico?: boolean;
  };
  requiere_supervisor_descuentos: boolean;
  configuracion_notificaciones?: {
    alertas_stock?: boolean;
    nuevas_ventas?: boolean;
    transferencias?: boolean;
  };
  created_at?: string;
  updated_at?: string;
}

export interface SucursalPerformance {
  id?: number;
  sucursal_id: number;
  ventas_dia: number;
  ventas_mes: number;
  ventas_año: number;
  transacciones_dia: number;
  transacciones_mes: number;
  ticket_promedio: number;
  productos_vendidos_dia: number;
  productos_vendidos_mes: number;
  productos_top_dia?: {
    id: number;
    nombre: string;
    cantidad: number;
    ingresos: number;
  }[];
  clientes_atendidos_dia: number;
  clientes_nuevos_mes: number;
  productos_bajo_stock: number;
  productos_sin_stock: number;
  valor_inventario: number;
  empleados_activos: number;
  ventas_por_empleado: number;
  ranking_empresa?: number;
  crecimiento_vs_mes_anterior: number;
  participacion_ventas_empresa: number;
  fecha_metricas: string;
  ultima_actualizacion?: string;
  created_at?: string;
  updated_at?: string;
}

export interface SucursalTransfer {
  id?: number;
  numero_transferencia: string;
  sucursal_origen_id: number;
  sucursal_destino_id: number;
  estado: 'pendiente' | 'en_transito' | 'recibida' | 'cancelada';
  tipo_transferencia: 'reposicion' | 'equilibrio' | 'devolucion' | 'manual';
  fecha_solicitud: string;
  fecha_envio?: string;
  fecha_recepcion?: string;
  usuario_solicita_id: number;
  usuario_envia_id?: number;
  usuario_recibe_id?: number;
  motivo?: string;
  observaciones?: string;
  documentos_adjuntos?: string[];
  total_items: number;
  valor_total: number;
  requiere_aprobacion: boolean;
  usuario_aprueba_id?: number;
  fecha_aprobacion?: string;
  sucursal_origen?: Sucursal;
  sucursal_destino?: Sucursal;
  usuario_solicita?: User;
  usuario_envia?: User;
  usuario_recibe?: User;
  usuario_aprueba?: User;
  items?: SucursalTransferItem[];
  created_at?: string;
  updated_at?: string;
}

export interface SucursalTransferItem {
  id?: number;
  sucursal_transfer_id: number;
  producto_id: number;
  codigo_producto: string;
  nombre_producto: string;
  unidad_medida?: string;
  cantidad_solicitada: number;
  cantidad_enviada: number;
  cantidad_recibida: number;
  stock_origen_antes?: number;
  stock_destino_antes?: number;
  precio_unitario: number;
  valor_linea: number;
  lote_numero?: string;
  fecha_vencimiento?: string;
  estado_item: 'pendiente' | 'parcial' | 'completo' | 'faltante';
  observaciones_item?: string;
  cantidad_diferencia: number;
  motivo_diferencia?: string;
  created_at?: string;
  updated_at?: string;
}

// Props para componentes de Company Management
export interface CompanySettingsIndexProps {
  empresa: Empresa & {
    plan?: { id: number; nombre: string };
    settings?: EmpresaSettings;
  };
  settings: EmpresaSettings;
  can: {
    update_settings: boolean;
    view_security: boolean;
  };
}

export interface CompanyBrandingIndexProps {
  empresa: Empresa;
  branding: OrganizationBranding;
  can: {
    update_branding: boolean;
  };
}

export interface CompanyAnalyticsIndexProps {
  empresa: Empresa;
  analytics: EmpresaAnalytics;
  can: {
    view_analytics: boolean;
    export_reports: boolean;
  };
}

export interface BranchManagementIndexProps {
  empresa: Empresa;
  sucursales: {
    data: (Sucursal & {
      settings?: SucursalSettings;
      performance?: SucursalPerformance;
    })[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  can: {
    create_branch: boolean;
    edit_branch: boolean;
    delete_branch: boolean;
  };
}

export interface BranchTransferIndexProps {
  empresa: Empresa;
  sucursales: Sucursal[];
  transfers: {
    data: SucursalTransfer[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  can: {
    create_transfer: boolean;
    manage_transfers: boolean;
  };
}
