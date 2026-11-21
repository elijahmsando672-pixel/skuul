<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    private User $studentUser;
    private User $teacherUser;
    private User $parentUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studentUser = $this->createRoleUser('student', 'student-role@example.test');
        $this->createStudentFixture($this->studentUser);

        $this->teacherUser = $this->createRoleUser('teacher', 'teacher-role@example.test');
        $this->createTeacherFixture($this->teacherUser);

        $this->parentUser = $this->createRoleUser('parent', 'parent-role@example.test');
        $this->createParentFixture($this->parentUser);
    }

    public function test_student_cannot_access_admin_area(): void
    {
        $this->assertAdminAreaIsForbiddenFor($this->studentUser);
    }

    public function test_teacher_cannot_access_admin_area(): void
    {
        $this->assertAdminAreaIsForbiddenFor($this->teacherUser);
    }

    public function test_parent_cannot_access_admin_area(): void
    {
        $this->assertAdminAreaIsForbiddenFor($this->parentUser);
    }

    private function assertAdminAreaIsForbiddenFor(User $user): void
    {
        foreach (['/admin/dashboard', '/admin/timetables', '/admin/fees'] as $uri) {
            $this->actingAs($user)->get($uri)->assertForbidden();
        }
    }
}
