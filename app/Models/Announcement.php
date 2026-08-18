<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'author_id', 'title', 'body', 'audience', 'pinned',
        'publish_at', 'expires_at', 'status',
    ];

    protected function casts(): array
    {
        return ['pinned' => 'boolean', 'publish_at' => 'datetime', 'expires_at' => 'datetime'];
    }

    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
}
