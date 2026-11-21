<x-app-layout>
    <x-slot name="title">Edit Book</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Edit Book</h2>
            <form method="POST" action="{{ route('admin.library.update', $book) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="title" class="form-label">Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $book->title) }}" class="input-field" required>
                </div>
                <div>
                    <label for="author" class="form-label">Author</label>
                    <input type="text" id="author" name="author" value="{{ old('author', $book->author) }}" class="input-field" required>
                </div>
                <div>
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}" class="input-field">
                </div>
                <div>
                    <label for="category" class="form-label">Category</label>
                    <select id="category" name="category" class="select-field" required>
                        @foreach(['Fiction', 'Non-Fiction', 'Science', 'Mathematics', 'History', 'Reference', 'Textbook'] as $category)
                            <option value="{{ $category }}" @selected(old('category', $book->category) === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="quantity" class="form-label">Total Copies</label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $book->quantity) }}" min="1" class="input-field" required>
                    </div>
                    <div>
                        <label for="available_quantity" class="form-label">Available Copies</label>
                        <input type="number" id="available_quantity" name="available_quantity" value="{{ old('available_quantity', $book->available_quantity) }}" min="0" class="input-field" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="published_year" class="form-label">Published Year</label>
                        <input type="number" id="published_year" name="published_year" value="{{ old('published_year', $book->published_year) }}" min="0" max="9999" class="input-field">
                    </div>
                    <div>
                        <label for="shelf_location" class="form-label">Shelf Location</label>
                        <input type="text" id="shelf_location" name="shelf_location" value="{{ old('shelf_location', $book->shelf_location) }}" class="input-field">
                    </div>
                </div>
                <button type="submit" class="btn-primary w-full">Save Changes</button>
            </form>
        </div>
        <a href="{{ route('admin.library.index') }}" class="btn-secondary w-full">Back to Library</a>
    </div>
</x-app-layout>
