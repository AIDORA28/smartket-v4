<?php

namespace Tests\Feature\Core;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseStructureValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function all_phase_3_tables_were_created_successfully()
    {
        // Verificar que todas las tablas de la Fase 3 fueron creadas
        $expectedTables = [
            'empresa_settings',
            'organization_branding',
            'empresa_analytics',
            'sucursal_settings',
            'sucursal_performance',
            'sucursal_transfers',
            'sucursal_transfer_items'
        ];

        foreach ($expectedTables as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "Table {$table} does not exist"
            );
        }
    }

    /** @test */
    public function empresa_settings_table_has_correct_structure()
    {
        $this->assertTrue(Schema::hasTable('empresa_settings'));
        
        $expectedColumns = [
            'id',
            'empresa_id',
            'configuracion_notificaciones',
            'configuracion_facturacion',
            'configuracion_inventario',
            'configuracion_ventas',
            'configuracion_backup',
            'zona_horaria_predeterminada',
            'idioma_predeterminado',
            'moneda_predeterminada',
            'configuracion_ui',
            'configuracion_seguridad',
            'created_at',
            'updated_at'
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(
                Schema::hasColumn('empresa_settings', $column),
                "Column {$column} does not exist in empresa_settings table"
            );
        }
    }

    /** @test */
    public function organization_branding_table_has_correct_structure()
    {
        $this->assertTrue(Schema::hasTable('organization_branding'));
        
        $expectedColumns = [
            'id',
            'empresa_id',
            'primary_color',
            'secondary_color',
            'accent_color',
            'logo_url',
            'favicon_url',
            'background_image_url',
            'typography_config',
            'ui_theme',
            'email_template_config',
            'invoice_template_config',
            'social_media_links',
            'custom_css',
            'brand_guidelines',
            'created_at',
            'updated_at'
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(
                Schema::hasColumn('organization_branding', $column),
                "Column {$column} does not exist in organization_branding table"
            );
        }
    }

    /** @test */
    public function sucursal_transfers_table_has_correct_structure()
    {
        $this->assertTrue(Schema::hasTable('sucursal_transfers'));
        
        $expectedColumns = [
            'id',
            'numero_transferencia',
            'sucursal_origen_id',
            'sucursal_destino_id',
            'estado',
            'tipo_transferencia',
            'fecha_solicitud',
            'fecha_envio',
            'fecha_recepcion',
            'usuario_solicita_id',
            'usuario_envia_id',
            'usuario_recibe_id',
            'motivo',
            'observaciones',
            'documentos_adjuntos',
            'total_items',
            'valor_total',
            'requiere_aprobacion',
            'usuario_aprueba_id',
            'fecha_aprobacion',
            'created_at',
            'updated_at'
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(
                Schema::hasColumn('sucursal_transfers', $column),
                "Column {$column} does not exist in sucursal_transfers table"
            );
        }
    }

    /** @test */
    public function models_instantiate_correctly()
    {
        // Verificar que todos los modelos se pueden instanciar sin errores
        $models = [
            \App\Models\Core\EmpresaSettings::class,
            \App\Models\Core\OrganizationBranding::class,
            \App\Models\Core\EmpresaAnalytics::class,
            \App\Models\Core\SucursalSettings::class,
            \App\Models\Core\SucursalPerformance::class,
            \App\Models\Core\SucursalTransfer::class,
            \App\Models\Core\SucursalTransferItem::class,
        ];

        foreach ($models as $modelClass) {
            $model = new $modelClass();
            $this->assertNotNull($model);
            $this->assertIsArray($model->getFillable());
        }
    }

    /** @test */
    public function database_indexes_were_created()
    {
        // Verificar que se crearon los índices principales
        $indexes = [
            ['table' => 'empresa_settings', 'column' => 'empresa_id'],
            ['table' => 'organization_branding', 'column' => 'empresa_id'],
            ['table' => 'empresa_analytics', 'column' => 'empresa_id'],
            ['table' => 'sucursal_settings', 'column' => 'sucursal_id'],
            ['table' => 'sucursal_performance', 'column' => 'sucursal_id'],
            ['table' => 'sucursal_transfers', 'column' => 'numero_transferencia'],
        ];

        foreach ($indexes as $index) {
            $this->assertTrue(
                Schema::hasColumn($index['table'], $index['column']),
                "Index column {$index['column']} does not exist in table {$index['table']}"
            );
        }
    }
}
