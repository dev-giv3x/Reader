<?php

namespace App\Http\Services;

use App\Models\Status;
use App\Models\User;

class StatusService
{
   public function index(User $user, ?string $status = null): array
{
    $query = Status::with('book')
        ->where('user_id', $user->id);

    if ($status !== null) {
        $query->where('status', $status);
    }

    return $query->get()->map(function ($status) {
        $book = $status->book;

        return [
            'id'     => $status->id,
            'status' => $status->status,
            'book'   => [
                'id'          => $book->id,
                'title'       => $book->title,
                'author'      => $book->author,
                'description' => $book->description,
                'file'        => $book->file,
            ],
        ];
    })->toArray();
}

    public function store(?User $user, string $bookId, string $statusValue): array
    {
        $status = Status::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $bookId],
            ['status' => $statusValue]
        );

        return [
            'id'     => $status->id,
            'status' => $status->status,
            'book_id' => $status->book_id,
        ];
    }

    public function destroy(?User $user, string $bookId): ?array
    {
        $status = Status::where('user_id', $user->id)
                        ->where('book_id', $bookId)
                        ->first();

        if (! $status) {
            return null;
        }

        $status->delete();

        return [
            'id'       => $status->id,
            'book_id'  => $status->book_id,
            'user_id'  => $status->user_id,
        ];
    }
}