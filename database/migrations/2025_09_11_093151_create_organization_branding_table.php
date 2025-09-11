<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organization_branding', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade')
                  ->comment('Relación con empresa');
            
            // Colores corporativos
            $table->string('primary_color', 7)->default('#3B82F6')
                  ->comment('Color primario en formato hex');
            $table->string('secondary_color', 7)->default('#64748B')
                  ->comment('Color secundario en formato hex');
            $table->string('accent_color', 7)->default('#F59E0B')
                  ->comment('Color de acento en formato hex');
            
            // Assets visuales
            $table->string('logo_url', 500)->nullable()
                  ->comment('URL del logo principal');
            $table->string('favicon_url', 500)->nullable()
                  ->comment('URL del favicon');
            $table->string('background_image_url', 500)->nullable()
                  ->comment('URL imagen de fondo');
            
            // Configuración tipográfica
            $table->json('typography_config')->nullable()
                  ->comment('Configuración fuentes y tipografía');
            
            // Tema de interfaz
            $table->enum('ui_theme', ['light', 'dark', 'auto'])->default('light')
                  ->comment('Tema de la interfaz');
            
            // Plantillas personalizadas
            $table->json('email_template_config')->nullable()
                  ->comment('Configuración plantillas email');
            $table->json('invoice_template_config')->nullable()
                  ->comment('Configuración plantillas facturación');
            
            // Redes sociales
            $table->json('social_media_links')->nullable()
                  ->comment('Enlaces redes sociales');
            
            // Configuración avanzada
            $table->json('custom_css')->nullable()
                  ->comment('CSS personalizado adicional');
            $table->json('brand_guidelines')->nullable()
                  ->comment('Guías de marca y uso');
            
            $table->timestamps();
            
            // Índices
            $table->unique('empresa_id', 'organization_branding_empresa_unique');
            $table->index('ui_theme');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_branding');
    }
};
