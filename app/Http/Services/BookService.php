<?php
namespace App\Http\Services;

use App\Models\Book;
use App\Models\User;

class BookService
{

    public function index(?User $user)
    {
        $booksQuery = $user && $user->is_admin
        ? Book::query()
        : Book::where('is_public', true);

        $books = $booksQuery->get();

        $books = $books->map(function ($book) {
            $avg = $book->averageRating();
            return array_merge(
                $book->toArray(),
                ['average_rating' => $avg !== null ? rtrim(rtrim(number_format($avg, 2, '.', ''), '0'), '.') : null]
            );
        });
        return $books;

    }

    public function store($data, $file, int $userId)
    {
        $filePath = $file->store('books', 'public');

    $book = Book::create([
        'title' => $data['title'],
        'author' => $data['author'],
        'description' => $data['description'],
        'is_public' => $data['is_public'] ?? false,
        'file' => $filePath,
        'user_id' => $userId,
    ]);

    return $book;
    }
    
   public function show(int $id, User $user): array|null|string{
        $book = Book::with(['comments.user', 'ratings.user'])->find($id);

        if (! $book) {
            return response()->json(['error' => 'Book not found.'], 404);
        }

        if (! $book->is_public && $book->user_id !== request()->user?->id) {
            return response()->json(['error' => 'You do not have permission to view this book.'], 403);
        }

        return [
            
                'id'             => $book->id,
                'title'          => $book->title,
                'author'         => $book->author,
                'description'    => $book->description,
                'file'           => $book->file,
                'is_public'      => $book->is_public,
                'user_id'        => $book->user_id,
                'comments'       => $book->comments->map(function ($comment) {
                    return [
                        'id'         => $comment->id,
                        'content'    => $comment->content,
                        'user'       => [
                            'id'   => $comment->user->id,
                            'name' => $comment->user->name,
                        ],
                        'created_at' => $comment->created_at,
                    ];
                }),
                'ratings'        => $book->ratings->map(function ($rating) {
                    return [
                        'id'         => $rating->id,
                        'value'      => $rating->value,
                        'user'       => [
                            'id'   => $rating->user->id,
                            'name' => $rating->user->name,
                        ],
                        'created_at' => $rating->created_at,
                    ];
                }),
                'average_rating' => $book->averageRating(),
            ];
    }

    public function myBooks(User $user){

        $books = Book::where('user_id', $user->id)->get();

    }

    public function search(string $query, ?User $user){

        return Book::where(function ($q) use ($query) {
            $q->where('title', 'like', "%$query%")
                ->orWhere('author', 'like', "%$query%");
        })
            ->where(function ($q) {
                $user = request()->user;

                $q->where('is_public', true);

                if ($user) {
                    $q->orWhere('user_id', $user->id);
                }
            })
            ->get();

    }
    public function destroy(){

        if ($request->user->is_admin) {
            $book = Book::find($id);

            if (! $book) {
                return response()->json(['message' => 'Book not found!'], 404);
            }

            $book->delete();
            return response()->json(['message' => 'Book deleted successfully!!'], 200);
        }

    }

}
