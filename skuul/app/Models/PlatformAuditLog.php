<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformAuditLog extends Model
{
    use HasFactory;
    protected $fillable = ['actor_id','school_id','action','target_type','target_id','meta','ip_address'];
    protected $casts = ['meta'=>'array'];

    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_id'); }
    public function school(): BelongsTo { return $this->belongsTo(School::class); }
}

