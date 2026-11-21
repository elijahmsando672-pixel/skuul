<?php

namespace Tests\Feature;

use Tests\TestCase;

class ImpersonationTest extends TestCase
{
    public function test_admin_can_impersonate_active_student(): void
    {
        $admin = $this->createRoleUser('admin', 'impersonation-admin@example.test');
        $student = $this->createRoleUser('student', 'impersonation-student@example.test');
        $this->createStudentFixture($student);

        $response = $this->actingAs($admin)
            ->post(route('admin.impersonate', $student))
            ->assertRedirect(route('dashboard'));

        $response->assertSessionHas('success');
        $this->assertAuthenticatedAs($student);
        $this->assertSame($admin->id, session('impersonator_id'));
    }

    public function test_impersonated_user_can_return_to_admin(): void
    {
        $admin = $this->createRoleUser('admin', 'return-admin@example.test');
        $student = $this->createRoleUser('student', 'return-student@example.test');
        $this->createStudentFixture($student);

        $this->actingAs($admin)->post(route('admin.impersonate', $student))->assertRedirect(route('dashboard'));

        $this->post(route('admin.impersonate.leave'))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('success');

        $this->assertAuthenticatedAs($admin);
        $this->assertNull(session('impersonator_id'));
    }

    public function test_non_admin_cannot_impersonate_user(): void
    {
        $teacher = $this->createRoleUser('teacher', 'blocked-teacher@example.test');
        $student = $this->createRoleUser('student', 'blocked-student@example.test');

        $this->actingAs($teacher)
            ->post(route('admin.impersonate', $student))
            ->assertForbidden();

        $this->assertAuthenticatedAs($teacher);
    }

    public function test_admin_cannot_impersonate_admin_or_inactive_user(): void
    {
        $admin = $this->createRoleUser('admin', 'guard-admin@example.test');
        $otherAdmin = $this->createRoleUser('admin', 'guard-other-admin@example.test');
        $inactiveStudent = $this->createRoleUser('student', 'guard-inactive@example.test');
        $inactiveStudent->update(['is_active' => false]);

        $this->actingAs($admin)
            ->post(route('admin.impersonate', $otherAdmin))
            ->assertForbidden();

        $this->post(route('admin.impersonate', $inactiveStudent))->assertForbidden();

        $this->assertAuthenticatedAs($admin);
    }
}
