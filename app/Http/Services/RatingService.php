<?php
namespace App\Http\Services;

use App\Models\Book;
use App\Models\Rating;
use App\Models\User;

class RatingService
{
    public function store(string $bookId, int $value, ?User $user): ?Rating
    {
        $book = Book::find($bookId);

        if (! $book || (!$book->is_public && $book->user_id !== $user?->id)) {
            return null;
        }

        return Rating::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            ['value' => $value]
        );
    }

    public function destroy(Rating $rating, ?User $user): bool
    {
        if ($rating->user_id !== $user?->id) {
            return false;
        }

        $rating->delete();

        return true;
    }
}