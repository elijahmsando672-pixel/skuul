<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'title',
        'author',
        'isbn',
        'category',
        'quantity',
        'available_quantity',
        'published_year',
        'shelf_location',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'available_quantity' => 'integer',
        'published_year' => 'integer',
    ];

    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'book_id');
    }

    public function activeIssues(): HasMany
    {
        return $this->issues()
            ->where('status', BookIssue::STATUS_ISSUED)
            ->whereNull('returned_at');
    }
}

