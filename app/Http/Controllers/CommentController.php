<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Book;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Services\CommentService;

class CommentController extends Controller
{
    public function __construct(private CommentService $commentService)
    {}

    public function index(string $bookId, Request $request)
    {
        $user = $request->user;

        $comments = $this->commentService->index($bookId, $user);

        if ($comments === null) {
            return response(['error' => 'Book not found or access denied.'], 403);
        }

        return ['data' => $comments];
    }

    public function store(string $bookId, StoreCommentRequest $request)
    {
         $user = request()->user;

        $comment = $this->commentService->store($bookId, $request->validated(), $user);

        if ($comment === null) {
            return response(['error' => 'Book not found or access denied.'], 403);
        }

        return response(['data' => $comment], 201);
    }

    public function destroy(Comment $comment, Request $request)
    {
        $user = $request->user;

        $deleted = $this->commentService->destroy($comment, $user);

        if ($deleted === null) {
            return response(['error' => 'You are not authorized to delete this comment.'], 403);
        }

        return ['message' => 'Comment deleted successfully.'];
    }
}