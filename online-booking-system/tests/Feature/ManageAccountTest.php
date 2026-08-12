<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ManageAccountTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_manage_account_page(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get(route('admin.manage-account'));

        $response->assertOk();
        $response->assertSee('Manage Account');
        $response->assertSee($admin->email);
    }

    public function test_admin_can_create_an_employee_account(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.manage-account.store'), [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'middle_initial' => 'A',
            'email' => 'jane.doe@casaul.com',
            'contact_no' => '09171234567',
            'role' => 'employee',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.manage-account'));

        $this->assertDatabaseHas('users', [
            'email' => 'jane.doe@casaul.com',
            'role' => 'employee',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'name' => 'Jane A. Doe',
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $user = User::where('email', 'jane.doe@casaul.com')->first();
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_created_account_can_log_in_on_other_sessions(): void
    {
        $this->actingAs($this->admin())->post(route('admin.manage-account.store'), [
            'first_name' => 'Marco',
            'last_name' => 'Reyes',
            'email' => 'marco@casaul.com',
            'role' => 'housekeeping',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'marco@casaul.com',
            'password' => 'secret123',
            'role' => 'housekeeping',
        ]);

        $response->assertRedirect(route('housekeeping.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_inactive_account_cannot_log_in(): void
    {
        $this->actingAs($this->admin())->post(route('admin.manage-account.store'), [
            'first_name' => 'Luis',
            'last_name' => 'Garcia',
            'email' => 'luis@casaul.com',
            'role' => 'employee',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $user = User::where('email', 'luis@casaul.com')->first();
        $user->update(['is_active' => false]);

        \Illuminate\Support\Facades\Auth::logout();

        $response = $this->post(route('login.submit'), [
            'email' => 'luis@casaul.com',
            'password' => 'secret123',
            'role' => 'employee',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_toggle_account_status(): void
    {
        $admin = $this->admin();

        $user = User::create([
            'name' => 'Kate',
            'email' => 'kate@casaul.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.manage-account.status', $user->id), [
            'is_active' => 0,
        ]);

        $response->assertRedirect(route('admin.manage-account'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => false]);
    }

    public function test_admin_can_update_and_reset_password(): void
    {
        $admin = $this->admin();

        $user = User::create([
            'name' => 'Kate',
            'email' => 'kate@casaul.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.manage-account.update', $user->id), [
            'first_name' => 'Kate',
            'last_name' => 'Smith',
            'email' => 'kate.smith@casaul.com',
            'role' => 'employee',
            'password' => 'newpass123',
            'password_confirmation' => 'newpass123',
        ]);

        $response->assertRedirect(route('admin.manage-account'));

        $updated = $user->fresh();
        $this->assertEquals('kate.smith@casaul.com', $updated->email);
        $this->assertTrue(Hash::check('newpass123', $updated->password));
    }

    public function test_admin_can_delete_an_account(): void
    {
        $admin = $this->admin();

        $user = User::create([
            'name' => 'To Delete',
            'email' => 'delete@casaul.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.manage-account.destroy', $user->id));

        $response->assertRedirect(route('admin.manage-account'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->delete(route('admin.manage-account.destroy', $admin->id));

        $response->assertRedirect(route('admin.manage-account'));
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_non_admin_cannot_access_manage_account(): void
    {
        $employee = User::create([
            'name' => 'Employee',
            'email' => 'emp@casaul.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        $response = $this->actingAs($employee)->get(route('admin.manage-account'));

        $response->assertForbidden();
    }
}
