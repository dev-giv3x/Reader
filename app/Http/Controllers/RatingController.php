<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\Book;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{

    public function store(StoreRatingRequest $request, string $id)
    {
        $book = Book::findOrFail($id);

        if (! $book->is_public && $book->user_id !== request()->user->id) {
            return response()->json(['error' => 'You do not have permission to rate this book.'], 403);
        }

        $rating = Rating::updateOrCreate(
            ['user_id' => request()->user->id, 'book_id' => $book->id],
            ['value' => $request->input('value')]
        );

        return response()->json(['data' => $rating], 201);
    }

    public function destroy(Rating $rating)
    {
        if ($rating->user_id !== request()->user->id) {
            return response()->json(['error' => 'You are not authorized to delete this rating.'], 403);
        }

        $rating->delete();

        return response()->json(['message' => 'Rating deleted successfully.'], 200);
    }
}
