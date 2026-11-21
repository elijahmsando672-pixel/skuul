<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use RuntimeException;

class LibraryController extends Controller
{
    public function books(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));
        $query = Book::query();

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        return response()->json($query->orderBy('title')->paginate($this->perPage($request))->withQueryString());
    }

    public function issues(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = BookIssue::with(['book', 'borrower']);

        if (! $this->isAdmin($request)) {
            if ($user->hasRole('student')) {
                $query->where('borrower_type', 'student')->where('borrower_id', $user->student?->id ?? 0);
            } elseif ($user->hasRole('teacher')) {
                $query->where('borrower_type', 'teacher')->where('borrower_id', $user->teacher?->id ?? 0);
            } elseif ($user->hasRole('parent')) {
                $query->where('borrower_type', 'student')->whereIn('borrower_id', $this->childIds($user) ?: [0]);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return response()->json($query->orderByDesc('issued_at')->paginate($this->perPage($request)));
    }

    public function issue(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'borrower_type' => ['required', 'in:student,teacher'],
            'borrower_id' => ['required', 'integer'],
            'issued_at' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issued_at'],
        ]);

        $table = $validated['borrower_type'] === 'student' ? 'students' : 'teachers';
        $request->validate([
            'borrower_id' => [Rule::exists($table, 'id')],
        ]);

        try {
            $issue = DB::transaction(function () use ($validated) {
                Book::whereKey($validated['book_id'])->lockForUpdate()->firstOrFail();

                $affected = Book::whereKey($validated['book_id'])
                    ->where('available_quantity', '>', 0)
                    ->decrement('available_quantity');

                if ($affected === 0) {
                    throw new RuntimeException('No copies of this book are currently available.');
                }

                return BookIssue::create([
                    'book_id' => $validated['book_id'],
                    'borrower_type' => $validated['borrower_type'],
                    'borrower_id' => $validated['borrower_id'],
                    'issued_at' => $validated['issued_at'],
                    'due_date' => $validated['due_date'],
                    'status' => BookIssue::STATUS_ISSUED,
                ]);
            });
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 409);
        }

        return response()->json($issue->load(['book', 'borrower']), 201);
    }

    public function returnIssue(BookIssue $bookIssue): JsonResponse
    {
        $this->ensureAdmin(request());

        try {
            $issue = DB::transaction(function () use ($bookIssue) {
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

                return $issue;
            });
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 409);
        }

        return response()->json($issue->load(['book', 'borrower']));
    }

    private function isAdmin(Request $request): bool
    {
        return (bool) $request->user()?->hasRole('admin');
    }

    private function ensureAdmin(Request $request): void
    {
        if (! $this->isAdmin($request)) {
            abort(403, 'Admin access required.');
        }
    }

    private function childIds(User $user): array
    {
        $parent = $user->parent;

        return $parent ? $parent->students()->pluck('id')->all() : [];
    }

    private function perPage(Request $request): int
    {
        return max(1, min(100, (int) $request->query('per_page', 15)));
    }
}
