<?php

/**
 * Sales Module - Model Aliases
 * 
 * Este archivo proporciona aliases para mantener compatibilidad retroactiva
 * con código que referencie los modelos de Sales en su ubicación anterior.
 * 
 * Uso: include este archivo en composer.json o en un Service Provider
 */

// Aliases para modelos del módulo Sales
class_alias('App\\Models\\Sales\\Venta', 'App\\Models\\Venta');
class_alias('App\\Models\\Sales\\VentaDetalle', 'App\\Models\\VentaDetalle');
class_alias('App\\Models\\Sales\\VentaPago', 'App\\Models\\VentaPago');
