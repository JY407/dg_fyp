<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Push a notification to every active resident user.
     */
    public static function pushToAll(string $type, string $title, string $message): void
    {
        $userIds = User::where('status', 'approved')
            ->where('user_type', '!=', 'admin')
            ->pluck('id');

        $now = now();
        $rows = $userIds->map(fn($id) => [
            'user_id'    => $id,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'read_at'    => null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        // Chunk inserts to avoid hitting DB param limits with large user bases
        foreach (array_chunk($rows, 500) as $chunk) {
            static::insert($chunk);
        }
    }
}
