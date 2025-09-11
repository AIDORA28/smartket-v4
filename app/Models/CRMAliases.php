<?php

// ==========================================
// ALIASES DEL MÓDULO CRM 
// ==========================================
// Archivo: app/Models/CRMAliases.php
// Propósito: Mantener compatibilidad con código legacy que use namespaces antiguos
// Creado: 11 Sep 2025

// Alias para Customer Relationship Management
class_alias(\App\Models\CRM\Cliente::class, \App\Models\Cliente::class);
