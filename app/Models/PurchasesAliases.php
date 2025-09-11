<?php

// ==========================================
// ALIASES DEL MÓDULO PURCHASES 
// ==========================================
// Archivo: app/Models/PurchasesAliases.php
// Propósito: Mantener compatibilidad con código legacy que use namespaces antiguos
// Creado: 11 Sep 2025

// Aliases para Purchases Module
class_alias(\App\Models\Purchases\Compra::class, \App\Models\Compra::class);
class_alias(\App\Models\Purchases\CompraItem::class, \App\Models\CompraItem::class);
class_alias(\App\Models\Purchases\Proveedor::class, \App\Models\Proveedor::class);
class_alias(\App\Models\Purchases\Recepcion::class, \App\Models\Recepcion::class);
