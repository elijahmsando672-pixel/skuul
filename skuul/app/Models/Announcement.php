<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'title',
        'content',
        'user_id',
        'target_audience',
        'priority',
        'starts_at',
        'expires_at',
        'is_published',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public const TARGET_ALL = 'all';
    public const TARGET_STUDENTS = 'students';
    public const TARGET_TEACHERS = 'teachers';
    public const TARGET_PARENTS = 'parents';
    public const TARGET_ADMIN = 'admin';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
