<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;
use App\Models\Teacher;
use App\Notifications\LibraryBookDue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use RuntimeException;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $books = Book::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return view('admin.library.index', compact('books'));
    }

    public function create()
    {
        return view('admin.library.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:32|unique:books,isbn',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1|max:100000',
            'available_quantity' => 'nullable|integer|min:0|lte:quantity',
            'published_year' => 'nullable|integer|digits:4|min:0|max:' . (now()->year + 1),
            'shelf_location' => 'nullable|string|max:255',
        ]);

        $validated['available_quantity'] ??= $validated['quantity'];

        Book::create($validated);

        return redirect()->route('admin.library.index')->with('success', 'Book created successfully.');
    }

    public function show(Book $book)
    {
        $book->load('issues.borrower');

        return view('admin.library.index', [
            'books' => Book::whereKey($book->id)->paginate(15),
            'book' => $book,
        ]);
    }

    public function edit(Book $book)
    {
        return view('admin.library.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => ['nullable', 'string', 'max:32', Rule::unique('books', 'isbn')->ignore($book->id)],
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1|max:100000',
            'available_quantity' => 'nullable|integer|min:0|lte:quantity',
            'published_year' => 'nullable|integer|digits:4|min:0|max:' . (now()->year + 1),
            'shelf_location' => 'nullable|string|max:255',
        ]);

        $activeIssues = $book->activeIssues()->count();
        $quantity = (int) $validated['quantity'];
        $available = (int) ($validated['available_quantity'] ?? $quantity);

        if ($quantity < $activeIssues || $available > $quantity - $activeIssues) {
            return redirect()->back()->withInput()->with('error', 'Adjust the copy counts to account for currently issued books.');
        }

        $validated['available_quantity'] = $available;
        $book->update($validated);

        return redirect()->route('admin.library.index')->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        if ($book->activeIssues()->exists()) {
            return redirect()->route('admin.library.index')->with('error', 'This book cannot be deleted while copies are issued.');
        }

        $book->delete();

        return redirect()->route('admin.library.index')->with('success', 'Book deleted successfully.');
    }

    public function issues()
    {
        $issues = BookIssue::with(['book', 'borrower'])
            ->orderByDesc('issued_at')
            ->paginate(15);

        $books = Book::orderBy('title')->get();
        $students = Student::with('user')->orderBy('id')->get();
        $teachers = Teacher::with('user')->orderBy('id')->get();

        return view('admin.library.issues', compact('issues', 'books', 'students', 'teachers'));
    }

    public function issue(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|integer|exists:books,id',
            'borrower_type' => 'required|in:student,teacher',
            'issued_at' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issued_at',
        ]);

        $table = $validated['borrower_type'] === 'student' ? 'students' : 'teachers';
        $validated['borrower_id'] = $request->validate([
            'borrower_id' => ['required', 'integer', Rule::exists($table, 'id')],
        ])['borrower_id'];

        $issueId = null;

        try {
            DB::transaction(function () use ($validated, &$issueId) {
                Book::whereKey($validated['book_id'])->lockForUpdate()->firstOrFail();

                $affected = Book::whereKey($validated['book_id'])
                    ->where('available_quantity', '>', 0)
                    ->decrement('available_quantity');

                if ($affected === 0) {
                    throw new RuntimeException('No copies of this book are currently available.');
                }

                $issue = BookIssue::create([
                    'book_id' => $validated['book_id'],
                    'borrower_type' => $validated['borrower_type'],
                    'borrower_id' => $validated['borrower_id'],
                    'issued_at' => $validated['issued_at'],
                    'due_date' => $validated['due_date'],
                    'status' => BookIssue::STATUS_ISSUED,
                ]);

                $issueId = $issue->id;
            });
        } catch (RuntimeException $exception) {
            return redirect()->route('admin.library.issues')->with('error', $exception->getMessage());
        }

        $borrower = $validated['borrower_type'] === 'student'
            ? Student::with('user')->find($validated['borrower_id'])
            : Teacher::with('user')->find($validated['borrower_id']);

        $issue = BookIssue::with('book')->find($issueId);

        if ($issue && $borrower?->user) {
            $borrower->user->notify(new LibraryBookDue($issue));
        }

        return redirect()->route('admin.library.issues')->with('success', 'Book issued successfully.');
    }

    public function returnBook(BookIssue $bookIssue)
    {
        try {
            DB::transaction(function () use ($bookIssue) {
                $issue = BookIssue::whereKey($bookIssue->id)->lockForUpdate()->firstOrFail();

                if ($issue->returned_at !== null || $issue->status === BookIssue::STATUS_RETURNED) {
                    throw new RuntimeException('This book has already been returned.');
                }

                $issue->update([
                    'returned_at' => now()->toDateString(),
                    'status' => BookIssue::STATUS_RETURNED,
                ]);

                Book::whereKey($issue->book_id)
                    ->whereColumn('available_quantity', '<', 'quantity')
                    ->increment('available_quantity');
            });
        } catch (RuntimeException $exception) {
            return redirect()->route('admin.library.issues')->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.library.issues')->with('success', 'Book returned successfully.');
    }
}
