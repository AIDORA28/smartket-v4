<?php

namespace Tests\Feature\Core;

use Tests\TestCase;
use App\Models\Core\User;
use App\Models\Core\Empresa;
use App\Models\Core\Sucursal;
use App\Models\Core\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class UserManagementIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $owner;
    protected $empresa;
    protected $sucursal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupTestEnvironment();
    }

    /**
     * Setup test environment with owner, empresa and sucursal
     */
    private function setupTestEnvironment()
    {
        // Create plan
        $plan = Plan::create([
            'nombre' => 'Plan Test',
            'precio_mensual' => 99.00,
            'max_usuarios' => 10,
            'max_productos' => 1000,
            'max_sucursales' => 3,
            'activo' => true,
            'visible' => true
        ]);

        // Create empresa
        $this->empresa = Empresa::create([
            'nombre' => 'Empresa Test',
            'plan_id' => $plan->id,
            'activa' => true
        ]);

        // Create sucursal
        $this->sucursal = Sucursal::create([
            'empresa_id' => $this->empresa->id,
            'nombre' => 'Sucursal Test',
            'activa' => true
        ]);

        // Create owner user
        $this->owner = User::create([
            'name' => 'Owner Test',
            'email' => 'owner@test.com',
            'password_hash' => bcrypt('password123'),
            'empresa_id' => $this->empresa->id,
            'sucursal_id' => $this->sucursal->id,
            'rol_principal' => 'owner',
            'activo' => true,
            'email_verified_at' => now()
        ]);
    }

    /** @test */
    public function owner_can_access_user_management_index()
    {
        $response = $this->actingAs($this->owner)
            ->get('/core/users');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Core/Users/Index')
                 ->has('users.data')
                 ->has('roles')
                 ->has('empresas')
        );
    }

    /** @test */
    public function owner_can_create_new_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'rol_principal' => 'admin',
            'empresa_id' => $this->empresa->id,
            'sucursal_id' => $this->sucursal->id,
            'activo' => true
        ];

        $response = $this->actingAs($this->owner)
            ->post('/core/users', $userData);

        $response->assertRedirect('/core/users');
        $response->assertSessionHas('success', 'Usuario creado exitosamente');

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'rol_principal' => 'admin',
            'empresa_id' => $this->empresa->id,
            'activo' => true
        ]);
    }

    /** @test */
    public function owner_can_edit_existing_user()
    {
        // Create a user to edit
        $user = User::create([
            'name' => 'Edit User',
            'email' => 'edituser@test.com',
            'password_hash' => bcrypt('password123'),
            'empresa_id' => $this->empresa->id,
            'sucursal_id' => $this->sucursal->id,
            'rol_principal' => 'vendedor',
            'activo' => true
        ]);

        $response = $this->actingAs($this->owner)
            ->get("/core/users/{$user->id}/edit");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Core/Users/Edit')
                 ->has('user')
                 ->has('roles')
                 ->where('user.name', 'Edit User')
        );
    }

    /** @test */
    public function owner_can_update_user_role()
    {
        $user = User::create([
            'name' => 'Update User',
            'email' => 'updateuser@test.com',
            'password_hash' => bcrypt('password123'),
            'empresa_id' => $this->empresa->id,
            'sucursal_id' => $this->sucursal->id,
            'rol_principal' => 'vendedor',
            'activo' => true
        ]);

        $updateData = [
            'name' => 'Updated User',
            'email' => 'updateuser@test.com',
            'rol_principal' => 'admin',
            'empresa_id' => $this->empresa->id,
            'sucursal_id' => $this->sucursal->id,
            'activo' => true
        ];

        $response = $this->actingAs($this->owner)
            ->put("/core/users/{$user->id}", $updateData);

        $response->assertRedirect('/core/users');
        $response->assertSessionHas('success', 'Usuario actualizado exitosamente');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated User',
            'rol_principal' => 'admin'
        ]);
    }

    /** @test */
    public function owner_can_delete_user()
    {
        $user = User::create([
            'name' => 'Delete User',
            'email' => 'deleteuser@test.com',
            'password_hash' => bcrypt('password123'),
            'empresa_id' => $this->empresa->id,
            'sucursal_id' => $this->sucursal->id,
            'rol_principal' => 'cajero',
            'activo' => true
        ]);

        $response = $this->actingAs($this->owner)
            ->delete("/core/users/{$user->id}");

        $response->assertRedirect('/core/users');
        $response->assertSessionHas('success', 'Usuario eliminado exitosamente');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }

    /** @test */
    public function non_owner_cannot_access_user_management()
    {
        $normalUser = User::create([
            'name' => 'Normal User',
            'email' => 'normal@test.com',
            'password_hash' => bcrypt('password123'),
            'empresa_id' => $this->empresa->id,
            'sucursal_id' => $this->sucursal->id,
            'rol_principal' => 'vendedor',
            'activo' => true
        ]);

        $response = $this->actingAs($normalUser)
            ->get('/core/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function user_filters_work_correctly()
    {
        // Create additional users with different roles
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password_hash' => bcrypt('password123'),
            'empresa_id' => $this->empresa->id,
            'sucursal_id' => $this->sucursal->id,
            'rol_principal' => 'admin',
            'activo' => true
        ]);

        User::create([
            'name' => 'Vendedor User',
            'email' => 'vendedor@test.com',
            'password_hash' => bcrypt('password123'),
            'empresa_id' => $this->empresa->id,
            'sucursal_id' => $this->sucursal->id,
            'rol_principal' => 'vendedor',
            'activo' => false
        ]);

        // Test search filter
        $response = $this->actingAs($this->owner)
            ->get('/core/users?search=Admin');

        $response->assertStatus(200);
        
        // Test role filter
        $response = $this->actingAs($this->owner)
            ->get('/core/users?role=vendedor');

        $response->assertStatus(200);

        // Test status filter
        $response = $this->actingAs($this->owner)
            ->get('/core/users?status=active');

        $response->assertStatus(200);
    }

    /** @test */
    public function complete_user_management_workflow()
    {
        // 1. Access user list
        $indexResponse = $this->actingAs($this->owner)
            ->get('/core/users');
        $indexResponse->assertStatus(200);

        // 2. Access create form
        $createResponse = $this->actingAs($this->owner)
            ->get('/core/users/create');
        $createResponse->assertStatus(200);

        // 3. Create new user
        $createData = [
            'name' => 'Workflow User',
            'email' => 'workflow@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'rol_principal' => 'admin',
            'empresa_id' => $this->empresa->id,
            'sucursal_id' => $this->sucursal->id,
            'activo' => true
        ];

        $storeResponse = $this->actingAs($this->owner)
            ->post('/core/users', $createData);
        $storeResponse->assertRedirect('/core/users');

        $user = User::where('email', 'workflow@test.com')->first();
        $this->assertNotNull($user);

        // 4. Edit user
        $editResponse = $this->actingAs($this->owner)
            ->get("/core/users/{$user->id}/edit");
        $editResponse->assertStatus(200);

        // 5. Update user
        $updateData = [
            'name' => 'Updated Workflow User',
            'email' => 'workflow@test.com',
            'rol_principal' => 'vendedor',
            'empresa_id' => $this->empresa->id,
            'sucursal_id' => $this->sucursal->id,
            'activo' => false
        ];

        $updateResponse = $this->actingAs($this->owner)
            ->put("/core/users/{$user->id}", $updateData);
        $updateResponse->assertRedirect('/core/users');

        // 6. Verify update
        $user->refresh();
        $this->assertEquals('Updated Workflow User', $user->name);
        $this->assertEquals('vendedor', $user->rol_principal);
        $this->assertFalse($user->activo);

        // 7. Delete user
        $deleteResponse = $this->actingAs($this->owner)
            ->delete("/core/users/{$user->id}");
        $deleteResponse->assertRedirect('/core/users');

        // 8. Verify deletion
        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }
}
