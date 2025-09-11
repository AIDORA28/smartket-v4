<?php

namespace Tests\Feature\Core;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CoreFrontendTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function core_navigation_structure_is_complete()
    {
        // Verificar que todas las rutas principales del Core existen
        $coreRoutes = [
            'core.company.settings.index',
            'core.company.analytics.index',
            'core.branches.index',
            'core.users.index'
        ];

        foreach ($coreRoutes as $routeName) {
            $this->assertTrue(
                Route::has($routeName),
                "Route {$routeName} does not exist"
            );
        }
    }

    /** @test */
    public function core_frontend_compilation_is_successful()
    {
        // Verificar que los archivos compilados existen
        $compiledFiles = [
            'public/build/manifest.json',
            'bootstrap/ssr/ssr-manifest.json'
        ];

        foreach ($compiledFiles as $file) {
            $this->assertFileExists(
                base_path($file),
                "Compiled file {$file} does not exist"
            );
        }
    }

    /** @test */
    public function core_typescript_interfaces_are_properly_defined()
    {
        $coreTypesFile = resource_path('js/types/core.ts');
        
        $this->assertFileExists($coreTypesFile, 'Core TypeScript types file does not exist');
        
        $content = file_get_contents($coreTypesFile);
        
        // Verificar que las interfaces principales están definidas
        $expectedInterfaces = [
            'EmpresaSettings',
            'OrganizationBranding',
            'EmpresaAnalytics',
            'SucursalSettings',
            'SucursalPerformance',
            'SucursalTransfer'
        ];

        foreach ($expectedInterfaces as $interface) {
            $this->assertStringContainsString(
                "interface {$interface}",
                $content,
                "Interface {$interface} is not defined in core.ts"
            );
        }
    }

    /** @test */
    public function core_components_exist_in_correct_structure()
    {
        $expectedComponents = [
            'resources/js/Pages/Core/CompanyManagement/Settings/Index.tsx',
            'resources/js/Pages/Core/CompanyManagement/Analytics/Index.tsx',
            'resources/js/Pages/Core/BranchManagement/Index.tsx'
        ];

        foreach ($expectedComponents as $component) {
            $this->assertFileExists(
                base_path($component),
                "Component {$component} does not exist"
            );
        }
    }

    /** @test */
    public function core_api_routes_are_registered()
    {
        // Verificar que las rutas API del Core están registradas
        $apiRoutes = [
            'api.core.core.empresas.index',
            'api.core.core.sucursales.index',
            'api.core.core.users.index',
            'api.core.core.planes.index'
        ];

        foreach ($apiRoutes as $routeName) {
            $this->assertTrue(
                Route::has($routeName),
                "API Route {$routeName} does not exist"
            );
        }
    }

    /** @test */
    public function core_database_tables_exist()
    {
        // Verificar que las tablas principales del Core existen
        $tables = [
            'core_users',
            'core_empresas',
            'core_sucursales',
            'core_planes',
            'core_plan_addons',
            'core_rubros',
            'core_empresa_rubros'
        ];

        foreach ($tables as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "Table {$table} does not exist"
            );
        }
    }

    /** @test */
    public function core_models_exist_and_are_loadable()
    {
        // Verificar que los modelos del Core se pueden cargar correctamente
        $models = [
            'App\Models\Core\User',
            'App\Models\Core\Empresa',
            'App\Models\Core\Sucursal',
            'App\Models\Core\Plan',
            'App\Models\Core\PlanAddon',
            'App\Models\Core\Rubro'
        ];

        foreach ($models as $model) {
            $this->assertTrue(
                class_exists($model),
                "Model {$model} does not exist"
            );
        }
    }
}
