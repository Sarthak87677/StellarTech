<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'school', 'grade', 'interest_area',
        'motivation', 'skills', 'project_preference', 'consent',
        'parental_consent', 'status', 'reviewed_by', 'reviewed_at',
        'review_note', 'created_user_id', 'ip',
    ];

    protected function casts(): array
    {
        return [
            'consent'          => 'boolean',
            'parental_consent' => 'boolean',
            'reviewed_at'      => 'datetime',
        ];
    }

    public function reviewer(): BelongsTo   { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function createdUser(): BelongsTo{ return $this->belongsTo(User::class, 'created_user_id'); }
}
