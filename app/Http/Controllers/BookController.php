<?php
namespace App\Http\Controllers;

use App\Http\Requests\SearchBookRequest;
use App\Http\Requests\StoreBookRequest;
use App\Http\Services\BookService;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(private BookService $bookService)
    {}

    public function index(Request $request)
    {

        $user  = $request->user;
        $books = $this->bookService->index($user);

        return response()->json(['data' => $books], 200);
    }

    public function store(StoreBookRequest $request)
    {
    $data = $request->validated();
    $file = $request->file('file'); 
    $userId = request()->user->id;

    $book = $this->bookService->store($data, $file, $userId);

        return response()->json(['data' => $book], 201);
    }

    public function show(int $id, ?User $user)
    {
          $bookData = $this->bookService->show($id, $user);

    if (isset($bookData['error'])) {
        return response()->json(['error' => $bookData['error']], $bookData['code']);
    }

    return response()->json(['data' => $bookData], 200);
    }

    public function myBooks()
    {
        $books = $this->bookService->myBooks($user);

        return response()->json(['data' => $books], 200);
    }

    public function search(SearchBookRequest $request)
    {

        $query = $request->get('query');
        $user = $request->user();
        $books = $this->bookService->serach($query, $user);


        return response()->json(['data' => $books], 200);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(Request $request, $id)
    {

        $user = $request->user();
        $books = $this->bookService->serach($query, $user);

        return response()->json(['message' => 'Forbidden for you!!!'], 403);
    }
}
