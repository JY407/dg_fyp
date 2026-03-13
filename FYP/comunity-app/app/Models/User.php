<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'unit_number',
        'block',
        'street',
        'user_type',
        'status',
        'created_by',
        'profile_photo_path',
    ];

    public function familyMembers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
    /**
     * Get the user's visitors.
     */
    public function visitors()
    {
        return $this->hasMany(Visitor::class);
    }

    /**
     * Get the events created by the user.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the events the user has joined.
     */
    public function joinedEvents()
    {
        return $this->belongsToMany(Event::class);
    }
    /**
     * Get the forum posts created by the user.
     */
    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    /**
     * Get the forum likes by the user.
     */
    public function forumLikes()
    {
        return $this->hasMany(ForumLike::class);
    }

    /**
     * Get the forum comments by the user.
     */
    public function forumComments()
    {
        return $this->hasMany(ForumComment::class);
    }

    /**
     * Get the facility bookings by the user.
     */
    public function facilityBookings()
    {
        return $this->hasMany(FacilityBooking::class);
    }
}
