<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role_id', 'status',
        'school', 'grade', 'avatar_path', 'joined_at',
    ];

    /** Never serialise secrets, even accidentally. */
    protected $hidden = [
        'password', 'remember_token', 'rakhandar_pass_hash',
    ];

    protected function casts(): array
    {
        return [
            'password'              => 'hashed',
            'rakhandar_pass_hash'   => 'hashed',
            'joined_at'             => 'datetime',
            'last_login_at'         => 'datetime',
            'locked_until'          => 'datetime',
            'email_verified_at'     => 'datetime',
            'rakhandar_pass_set_at' => 'datetime',
        ];
    }

    public function role(): BelongsTo          { return $this->belongsTo(Role::class); }
    public function credit(): HasOne           { return $this->hasOne(Credit::class); }
    public function streak(): HasOne           { return $this->hasOne(UserStreak::class); }
    public function streakLogs(): HasMany      { return $this->hasMany(StreakLog::class); }
    public function reflections(): HasMany     { return $this->hasMany(Reflection::class); }
    public function tasks(): HasMany           { return $this->hasMany(Task::class, 'assigned_to'); }
    public function uploads(): HasMany         { return $this->hasMany(MediaItem::class, 'uploader_id'); }

    /* ---------------- role helpers ----------------
       Checks compare the SLUG, never the numeric level. A level comparison
       would silently grant founder powers if a level value were ever changed.
    ------------------------------------------------ */

    public function hasRole(string ...$slugs): bool
    {
        return in_array($this->role->slug, $slugs, true);
    }

    public function isFounder(): bool
    {
        return $this->role->slug === 'founder';
    }

    /** Rakhandar eligibility — role only. Pass + OTP are checked separately. */
    public function mayAccessRakhandar(): bool
    {
        return $this->status === 'active'
            && $this->hasRole('founder', 'selected_oc');
    }

    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /* ---------------- lookups for the public site ---------------- */

    public static function founder(): ?self
    {
        return static::whereHas('role', fn ($q) => $q->where('slug', 'founder'))
            ->where('status', 'active')
            ->first();
    }

    public static function committee()
    {
        return static::whereHas('role', fn ($q) => $q->whereIn('slug', ['selected_oc', 'oc_member']))
            ->where('status', 'active')
            ->with('role')
            ->orderBy('name')
            ->get();
    }
}
