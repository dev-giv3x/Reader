<?php
namespace App\Http\Services;

use App\Models\Book;
use App\Models\User;

class BookService
{
    public function index(?User $user)
    {
        $query = $user && $user->is_admin
            ? Book::query()
            : Book::where('is_public', true);

        $books = $query->get();

        return $books->map(function ($book) {
            $avg = $book->averageRating();
            return array_merge(
                $book->toArray(),
                ['average_rating' => $avg !== null ? rtrim(rtrim(number_format($avg, 2, '.', ''), '0'), '.') : null]
            );
        });
    }

    public function store(array $data, $file, int $userId): Book
    {
        $filePath = $file->store('books', 'public');

        return Book::create([
            'title'       => $data['title'],
            'author'      => $data['author'],
            'description' => $data['description'],
            'is_public'   => $data['is_public'] ?? false,
            'file'        => $filePath,
            'user_id'     => $userId,
        ]);
    }

    public function show(int $id, ?User $user): ?array
    {
        $book = Book::with(['comments.user', 'ratings.user'])->find($id);

        if (! $book) {
            return null;
        }

        if (! $book->is_public && $book->user_id !== $user?->id) {
            return null;
        }

        return [
            'id'             => $book->id,
            'title'          => $book->title,
            'author'         => $book->author,
            'description'    => $book->description,
            'file'           => $book->file,
            'is_public'      => $book->is_public,
            'user_id'        => $book->user_id,
            'comments'       => $book->comments->map(fn($c) => [
                'id'         => $c->id,
                'content'    => $c->content,
                'user'       => [
                    'id'   => $c->user->id,
                    'name' => $c->user->name,
                ],
                'created_at' => $c->created_at,
            ]),
            'ratings'        => $book->ratings->map(fn($r) => [
                'id'         => $r->id,
                'value'      => $r->value,
                'user'       => [
                    'id'   => $r->user->id,
                    'name' => $r->user->name,
                ],
                'created_at' => $r->created_at,
            ]),
            'average_rating' => $book->averageRating(),
        ];
    }

    public function myBooks(User $user)
    {
        return Book::where('user_id', $user->id)->get();
    }

    public function search(string $query, ?User $user)
    {
        return Book::where(function ($q) use ($query) {
            $q->where('title', 'like', "%$query%")
              ->orWhere('author', 'like', "%$query%");
        })
        ->where(function ($q) use ($user) {
            $q->where('is_public', true);

            if ($user) {
                $q->orWhere('user_id', $user->id);
            }
        })
        ->get();
    }

    public function destroy(int $id, User $user): ?Book
    {
        if (! $user->is_admin) {
            return null;
        }

        $book = Book::find($id);

        if (! $book) {
            return null;
        }

        $book->delete();

        return $book;
    }
}
