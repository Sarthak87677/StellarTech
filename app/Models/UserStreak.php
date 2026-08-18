<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStreak extends Model
{
    protected $fillable = [
        'user_id', 'current_streak', 'longest_streak', 'last_active_date',
        'weekly_goal_minutes', 'week_minutes', 'total_points', 'recoveries_left',
    ];

    protected function casts(): array
    {
        return ['last_active_date' => 'date'];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
