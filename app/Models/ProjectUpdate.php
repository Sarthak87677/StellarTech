<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectUpdate extends Model
{
    protected $fillable = [
        'project_id', 'author_id', 'title', 'body', 'update_date',
        'visibility', 'is_approved', 'approved_by',
    ];

    protected function casts(): array
    {
        return ['update_date' => 'date', 'is_approved' => 'boolean'];
    }

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function author(): BelongsTo  { return $this->belongsTo(User::class, 'author_id'); }

    public function scopePubliclyVisible(Builder $q): Builder
    {
        return $q->where('visibility', 'public')->where('is_approved', true)
                 ->orderByDesc('update_date');
    }
}
