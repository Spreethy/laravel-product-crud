<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_index(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertOk();
    }

    public function test_staff_cannot_access_users_index(): void
    {
        $staff = User::factory()->create();

        $response = $this->actingAs($staff)->get(route('users.index'));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_from_users_index(): void
    {
        $this->get(route('users.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_create_a_staff_user(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'New Staff',
            'email' => 'newstaff@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => Role::Staff->value,
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'newstaff@example.com',
            'role' => Role::Staff->value,
        ]);
    }

    public function test_staff_cannot_create_a_user(): void
    {
        $staff = User::factory()->create();

        $response = $this->actingAs($staff)->post(route('users.store'), [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => Role::Staff->value,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'hacker@example.com']);
    }

    public function test_admin_can_update_a_user_role(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();

        $response = $this->actingAs($admin)->put(route('users.update', $staff), [
            'name' => $staff->name,
            'email' => $staff->email,
            'password' => '',
            'role' => Role::Admin->value,
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'role' => Role::Admin->value,
        ]);
    }

    public function test_admin_can_delete_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();

        $response = $this->actingAs($admin)->delete(route('users.destroy', $staff));

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->delete(route('users.destroy', $admin));

        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_last_admin_cannot_be_demoted_or_deleted(): void
    {
        $admin = User::factory()->admin()->create();

        $demote = $this->actingAs($admin)->put(route('users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'password' => '',
            'role' => Role::Staff->value,
        ]);
        $demote->assertSessionHasErrors();

        $delete = $this->actingAs($admin)->delete(route('users.destroy', $admin));
        $delete->assertSessionHasErrors();

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => Role::Admin->value]);
    }
}
