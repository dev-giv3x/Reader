<?php
namespace App\Http\Services;

use App\Models\Book;
use App\Models\Comment;
use App\Models\User;

class CommentService
{
    public function index(string $bookId, ?User $user): ?object
    {
        $book = Book::find($bookId);

        if (! $book || (! $book->is_public && $book->user_id !== $user?->id)) {
            return null;
        }

        return $book->comments()->with('user')->get();
    }

    public function store(string $bookId, array $data, ?User $user): ?Comment
    {
        $book = Book::find($bookId);

        if (! $book || (! $book->is_public && $book->user_id !== $user?->id)) {
            return null;
        }

        return $book->comments()->create([
            'content' => $data['content'],
            'user_id' => $user->id,
        ]);
    }

   
    public function destroy(Comment $comment, ?User $user)
{
    if (!$user) {
        return null;
    }

    if ($comment->user_id !== $user->id && !$user->is_admin) {
        return null;
    }

    return $comment->delete();
}
}