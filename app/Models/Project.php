<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'slug', 'title', 'summary', 'description', 'discipline',
        'stage', 'visibility', 'lead_user_id', 'cover_media_id',
        'started_at', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['started_at' => 'date'];
    }

    public function getRouteKeyName(): string  { return 'slug'; }

    public function lead(): BelongsTo     { return $this->belongsTo(User::class, 'lead_user_id'); }
    public function updates(): HasMany    { return $this->hasMany(ProjectUpdate::class); }
    public function tasks(): HasMany      { return $this->hasMany(Task::class); }
    public function media(): HasMany      { return $this->hasMany(MediaItem::class); }

    public function scopePubliclyVisible(Builder $q): Builder
    {
        return $q->where('visibility', 'public');
    }

    /** Drives the honest stage chip in the UI. */
    public function stageLabel(): string
    {
        return match ($this->stage) {
            'planned'   => 'Planned',
            'prototype' => 'Prototype',
            'testing'   => 'Testing',
            'active'    => 'Active',
            'completed' => 'Completed',
            default     => 'Planned',
        };
    }
}
