<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\SearchBookRequest;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    
    public function index()
    {
        $books = Book::where('is_public', true)->get();
        return response()->json(['data' => $books], 200);
    }

   
    public function store(StoreBookRequest $request)
    {
        // dd($request->all(), $request->user);

        $book = Book::create([
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'description' => $request->input('description'),
            'is_public' => $request->is_public,
            'file'=>$request->file->store('books', 'public'),
            // 'user_id' => $request->user->id,
            'user_id'=>request()->user->id,
        ]);

        // dd($book);

        return response()->json(['data' => $book], 201);
    }
    


//ПРОВЕРИТЬ РАБОТОСПОСОБНОСТЬ СТОРА

    // public function book(Request $request){
    //     // dd($request->user);

    //     $validated = $request->validate([
    //         'title' => 'required|string|max:75',
    //         'author' => 'required|string|max:50',
    //         'description' => 'required|string',
    //         'is_public' => 'required|boolean',
    //         'file' => 'required|file|mimes:pdf,epub|max:10000',
    //     ]);

    //     // dd($validated['title']);
     
    //     $book = Book::create([
    //         'title' => $validated['title'],
    //         'author' => $validated['author'],
    //         'description' => $validated['description'],
    //         'is_public' => $validated['is_public'],
    //         'file'=>$validated['file']->store('books', 'public'),
    //         'user_id' => $request->user->id,
    //     ]);

    //     // dd($book);

    //     return response()->json(['data' => $book], 201);
    // }



    
    public function show(string $id)
    {
        $book = Book::with(['comments.user', 'ratings.user'])->find($id);
    
        if (!$book) {
            return response()->json(['error' => 'Book not found.'], 404);
        }
    
        if (!$book->is_public && $book->user_id !== request()->user?->id) {
            return response()->json(['error' => 'You do not have permission to view this book.'], 403);
        }
    
        return response()->json([
            'data' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'description' => $book->description,
                'file' => $book->file,
                'is_public' => $book->is_public,
                'user_id' => $book->user_id,
                'comments' => $book->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                        ],
                        'created_at' => $comment->created_at,
                    ];
                }),
                'ratings' => $book->ratings->map(function ($rating) {
                    return [
                        'id' => $rating->id,
                        'value' => $rating->value,
                        'user' => [
                            'id' => $rating->user->id,
                            'name' => $rating->user->name,
                        ],
                        'created_at' => $rating->created_at,
                    ];
                }),
                'average_rating' => $book->averageRating(),
            ]
        ], 200);
    }

    public function myBooks()
    {
        $user = request()->user;

        $books = Book::where('user_id', $user->id)->get();

        return response()->json(['data' => $books], 200);
    }

   
    public function search(SearchBookRequest $request): JsonResponse
    {
    $query = $request->get('query');

    $books = Book::where(function ($q) use ($query) {
        $q->where('title', 'like', "%$query%")
          ->orWhere('author', 'like', "%$query%");
    })
    ->where(function ($q) {
        $user = request()->user();
        $q->where('is_public', true);
        if ($user) {
            $q->orWhere('user_id', $user->id);
        }
    })
    ->get();

    return response()->json(['data' => $books], 200);
    }


    public function update(Request $request, string $id)
    {
        //
    }

  
    public function destroy(string $id)
    {
        //
    }
}
