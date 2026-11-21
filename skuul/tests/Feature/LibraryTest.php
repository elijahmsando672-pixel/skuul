<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;
use App\Models\User;
use Tests\TestCase;

class LibraryTest extends TestCase
{
    private User $admin;
    private User $studentUser;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createRoleUser('admin', 'admin-library@example.test');

        $this->studentUser = $this->createRoleUser('student', 'student-library@example.test');
        $this->student = $this->createStudentFixture($this->studentUser);
    }

    public function test_admin_can_create_book_issue_it_and_return_it(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.library.store'), [
                'title' => 'Laravel Fundamentals',
                'author' => 'Taylor Otwell',
                'isbn' => '9780000000001',
                'category' => 'Programming',
                'quantity' => 2,
            ])
            ->assertRedirect(route('admin.library.index'));

        $book = Book::where('isbn', '9780000000001')->firstOrFail();

        $this->assertSame(2, $book->available_quantity);

        $this->actingAs($this->admin)
            ->post(route('admin.library.issues.store'), [
                'book_id' => $book->id,
                'borrower_type' => 'student',
                'borrower_id' => $this->student->id,
                'issued_at' => '2026-11-20',
                'due_date' => '2026-12-04',
            ])
            ->assertRedirect(route('admin.library.issues'));

        $issue = BookIssue::firstOrFail();

        $this->assertSame(BookIssue::STATUS_ISSUED, $issue->status);
        $this->assertSame(1, $book->fresh()->available_quantity);

        $this->actingAs($this->admin)
            ->put(route('admin.library.issues.return', $issue))
            ->assertRedirect(route('admin.library.issues'));

        $issue->refresh();

        $this->assertSame(BookIssue::STATUS_RETURNED, $issue->status);
        $this->assertNotNull($issue->returned_at);
        $this->assertSame(2, $book->fresh()->available_quantity);
    }

    public function test_issuing_book_with_zero_available_copies_fails(): void
    {
        $book = Book::create([
            'title' => 'Unavailable Book',
            'author' => 'Test Author',
            'isbn' => '9780000000002',
            'category' => 'Fiction',
            'quantity' => 1,
            'available_quantity' => 0,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.library.issues.store'), [
                'book_id' => $book->id,
                'borrower_type' => 'student',
                'borrower_id' => $this->student->id,
                'issued_at' => '2026-11-20',
                'due_date' => '2026-12-04',
            ])
            ->assertRedirect(route('admin.library.issues'))
            ->assertSessionHas('error', 'No copies of this book are currently available.');

        $this->assertDatabaseCount('book_issues', 0);
        $this->assertSame(0, $book->fresh()->available_quantity);
    }
}
