<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'isbn' => '978-0-06-112008-4',
                'category' => 'Fiction',
                'quantity' => 5,
                'available_quantity' => 5,
                'published_year' => 1960,
                'shelf_location' => 'A-01',
            ],
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'isbn' => '978-0-7432-7356-5',
                'category' => 'Fiction',
                'quantity' => 4,
                'available_quantity' => 4,
                'published_year' => 1925,
                'shelf_location' => 'A-02',
            ],
            [
                'title' => 'A Brief History of Time',
                'author' => 'Stephen Hawking',
                'isbn' => '978-0-553-38016-3',
                'category' => 'Science',
                'quantity' => 3,
                'available_quantity' => 3,
                'published_year' => 1988,
                'shelf_location' => 'B-04',
            ],
            [
                'title' => 'Mathematics for Elementary Teachers',
                'author' => 'Sybil B. Randolph',
                'isbn' => '978-0-07-352147-3',
                'category' => 'Mathematics',
                'quantity' => 6,
                'available_quantity' => 6,
                'published_year' => 2011,
                'shelf_location' => 'C-07',
            ],
        ];

        $seededBooks = [];

        foreach ($books as $data) {
            $attributes = $data;
            unset($attributes['available_quantity']);

            $book = Book::updateOrCreate(
                ['isbn' => $data['isbn']],
                $attributes
            );

            if ($book->wasRecentlyCreated) {
                $book->update(['available_quantity' => $data['quantity']]);
            }

            $seededBooks[] = $book;
        }

        $students = Student::query()->with('user')->orderBy('id')->limit(2)->get();
        $teachers = Teacher::query()->with('user')->orderBy('id')->limit(2)->get();

        $borrowers = [];

        foreach ($students as $student) {
            $borrowers[] = ['borrower_type' => 'student', 'borrower_id' => $student->id];
        }

        foreach ($teachers as $teacher) {
            $borrowers[] = ['borrower_type' => 'teacher', 'borrower_id' => $teacher->id];
        }

        foreach ($borrowers as $index => $borrower) {
            $book = $seededBooks[$index % count($seededBooks)];
            $issuedAt = '2026-11-' . sprintf('%02d', 1 + ($index % 5));
            $dueDate = $index % 2 === 0 ? '2026-11-10' : '2026-12-10';

            BookIssue::firstOrCreate(
                [
                    'book_id' => $book->id,
                    'borrower_type' => $borrower['borrower_type'],
                    'borrower_id' => $borrower['borrower_id'],
                    'issued_at' => $issuedAt,
                ],
                [
                    'due_date' => $dueDate,
                    'returned_at' => null,
                    'status' => BookIssue::STATUS_ISSUED,
                ]
            );

        }

        foreach ($seededBooks as $book) {
            $book->update([
                'available_quantity' => max(0, $book->quantity - $book->activeIssues()->count()),
            ]);
        }
    }
}
