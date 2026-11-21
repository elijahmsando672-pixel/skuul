<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;

class LibraryController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;

        $books = Book::orderBy('title')->paginate(15);

        $issuedBooks = $teacher
            ? BookIssue::with('book')
                ->where('borrower_type', 'teacher')
                ->where('borrower_id', $teacher->id)
                ->orderByDesc('issued_at')
                ->get()
            : collect();

        return view('teacher.library.index', compact('books', 'issuedBooks', 'teacher'));
    }
}
