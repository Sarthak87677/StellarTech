<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaItem extends Model
{
    protected $fillable = [
        'uploader_id', 'project_id', 'type', 'path', 'public_path', 'thumb_path',
        'embed_url', 'mime', 'size', 'duration', 'checksum', 'caption',
        'captured_at', 'tags', 'team_names', 'visibility', 'status',
        'is_featured_home', 'sort_order', 'exif_stripped',
    ];

    protected function casts(): array
    {
        return [
            'tags'             => 'array',
            'captured_at'      => 'date',
            'is_featured_home' => 'boolean',
            'exif_stripped'    => 'boolean',
        ];
    }

    public function uploader(): BelongsTo  { return $this->belongsTo(User::class, 'uploader_id'); }
    public function project(): BelongsTo   { return $this->belongsTo(Project::class); }
    public function approvals(): HasMany   { return $this->hasMany(MediaApproval::class, 'media_id'); }

    /**
     * The ONLY scope any public route may use.
     *
     * Three conditions, not one: approved, marked public, and actually
     * published to the public disk. A record can claim public status but if
     * public_path is null it was never copied out of private storage — so we
     * refuse to render it rather than emit a broken or leaking URL.
     */
    public function scopePubliclyVisible(Builder $q): Builder
    {
        return $q->where('status', 'approved')
                 ->where('visibility', 'public')
                 ->where(function ($q) {
                     $q->whereNotNull('public_path')
                       ->orWhere('type', 'embed');
                 });
    }

    public function scopeForMembers(Builder $q): Builder
    {
        return $q->where('status', 'approved')
                 ->whereIn('visibility', ['public', 'members']);
    }
}
