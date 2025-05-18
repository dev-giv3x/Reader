<?php
namespace App\Http\Controllers;

use App\Http\Requests\SearchBookRequest;
use App\Http\Requests\StoreBookRequest;
use App\Http\Services\BookService;
use App\Models\User;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(private BookService $bookService)
    {}

    public function index(Request $request)
    {
        $user  = $request->user;
        $books = $this->bookService->index($user);
        return ['data' => $books];
    }

    public function store(StoreBookRequest $request)
    {
        $data   = $request->validated();
        $file   = $request->file('file');
        $userId = request()->user->id;

        $book = $this->bookService->store($data, $file, $userId);

        return ['data' => $book];
    }

    public function show(int $id, Request $request)
    {
        $user = $request->user;
        $result = $this->bookService->show($id, $user);

        if ($result === null) {
            return response(['error' => 'Book not found or access denied'], 404);
        }

        return ['data' => $result];
    }

    public function myBooks(Request $request)
    {
        $user  = request()->user;
        $books = $this->bookService->myBooks($user);

        return ['data' => $books];
    }

    public function search(SearchBookRequest $request)
    {
        $query = $request->get('query');
        $user  = request()->user;
        $books = $this->bookService->search($query, $user);

        return ['data' => $books];
    }

    public function destroy(int $id, Request $request)
    {
        $user  = request()->user;
        $book = $this->bookService->destroy($id, $user);

        if ($book === null) {
            if (! $user->is_admin) {
                return response(['message' => 'Forbidden'], 403);
            }
            return response(['message' => 'Book not found'], 404);
        }

        return ['message' => 'Book deleted successfully!'];
    }
}
