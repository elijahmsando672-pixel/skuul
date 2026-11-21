<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;

class LibraryController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        $books = Book::orderBy('title')->paginate(15);

        $issuedBooks = $student
            ? BookIssue::with('book')
                ->where('borrower_type', 'student')
                ->where('borrower_id', $student->id)
                ->orderByDesc('issued_at')
                ->get()
            : collect();

        return view('student.library.index', compact('books', 'issuedBooks', 'student'));
    }
}
