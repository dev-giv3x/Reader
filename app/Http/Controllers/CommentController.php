<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Book;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    public function index(string $id)
    {
        $book = Book::findOrFail($id);

        if (! $book->is_public && $book->user_id !== request()->user->id) {
            return response()->json(['error' => 'Forbidden for you!!!'], 403);
        }

        return response()->json(['data' => $book->comments()->with('user')->get()]);
    }

    public function store(string $id, StoreCommentRequest $request)
    {

        $book = Book::findOrFail($id);

        if (! $book->is_public && $book->user_id !== request()->user->id) {
            return response()->json(['error' => 'Forbidden for you!!'], 403);
        }

        $comment = $book->comments()->create([
            'content' => $request->input('content'),
            'user_id' => request()->user->id,
        ]);

        return response()->json(['data' => $comment], 201);
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== request()->user?->id) {
            return response()->json(['error' => 'You are not authorized to delete this comment.'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.'], 200);
    }

}
