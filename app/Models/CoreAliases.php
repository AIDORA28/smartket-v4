<?php

// ==========================================
// ALIASES DEL MÓDULO CORE 
// ==========================================
// Archivo: app/Models/CoreAliases.php
// Propósito: Mantener compatibilidad con código legacy que use namespaces antiguos
// Creado: 11 Sep 2025

// Aliases para Core Module
class_alias(\App\Models\Core\User::class, \App\Models\User::class);
class_alias(\App\Models\Core\Empresa::class, \App\Models\Empresa::class);
class_alias(\App\Models\Core\Sucursal::class, \App\Models\Sucursal::class);
class_alias(\App\Models\Core\Plan::class, \App\Models\Plan::class);
class_alias(\App\Models\Core\PlanAddon::class, \App\Models\PlanAddon::class);
class_alias(\App\Models\Core\EmpresaAddon::class, \App\Models\EmpresaAddon::class);
class_alias(\App\Models\Core\Rubro::class, \App\Models\Rubro::class);
class_alias(\App\Models\Core\EmpresaRubro::class, \App\Models\EmpresaRubro::class);
class_alias(\App\Models\Core\FeatureFlag::class, \App\Models\FeatureFlag::class);
class_alias(\App\Models\Core\MetodoPago::class, \App\Models\MetodoPago::class);
class_alias(\App\Models\Core\UserEmpresaAcceso::class, \App\Models\UserEmpresaAcceso::class);
class_alias(\App\Models\Core\UserSucursalAcceso::class, \App\Models\UserSucursalAcceso::class);