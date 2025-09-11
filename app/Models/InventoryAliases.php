<?php

// Alias para compatibilidad hacia atrás
// Permite que el código existente funcione sin cambios

// Modelos del Módulo Inventory
class_alias(\App\Models\Inventory\Categoria::class, 'App\Models\Categoria');
class_alias(\App\Models\Inventory\Marca::class, 'App\Models\Marca');
class_alias(\App\Models\Inventory\UnidadMedida::class, 'App\Models\UnidadMedida');
class_alias(\App\Models\Inventory\Producto::class, 'App\Models\Producto');
class_alias(\App\Models\Inventory\ProductoStock::class, 'App\Models\ProductoStock');
class_alias(\App\Models\Inventory\InventarioMovimiento::class, 'App\Models\InventarioMovimiento');
