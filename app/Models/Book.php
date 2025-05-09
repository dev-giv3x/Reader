<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'description',
        'is_public',
        'file',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->HasMany(Comment::class);
    }

    public function ratings(): HasMany
    {
        return $this->HasMany(Rating::class);
    }

    public function readers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'book_user')
        ->withPivot('status')
        ->withTimestamps();
    }

    public function averageRating(): float
    {
        return (float) $this->ratings()->avg('value');
    }

    public function getUserRating(User $user): ?int
    {
        $rating = $this->ratings()->where('user_id', $user->id)->first();
        
        return $rating?->value;
    }

    public function getUserStatus(User $user): ?string
    {
        return $this->readers()->where('user_id', $user->id)->first()?->pivot->status;
    }

    public function isPublic(): bool
    {
        return $this->is_public;
    }

}
