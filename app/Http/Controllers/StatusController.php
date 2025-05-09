<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreStatusRequest;
use App\Models\Book;
use App\Models\Status;

class StatusController extends Controller
{
  
    public function index(Request $request)
    {
        $userId = request()->user->id;

        $statusType = $request->query('status');
    
        $query = Status::with('book')->where('user_id', $userId);
    
        if ($statusType) {
            $query->where('status', $statusType);
        }
    
        $statuses = $query->get();
    
        return response()->json(['data' => $statuses]);
    }


    public function store(StoreStatusRequest $request, string $id)
    {
        $userId = request()->user->id;

        $status = Status::updateOrCreate(
            ['user_id' => $userId, 'book_id' => $id],
            ['status' => $request->status]
        );
    
        return response()->json(['message' => 'Status updated.', 'data' => $status], 200);
    }

  
    public function show(string $id)
    {
        //
    }

 
    public function update(Request $request, string $id)
    {
        //
    }

   
    public function destroy(string $id)
    {
        $userId = request()->user->id;

    $status = Status::where('user_id', $userId)->where('book_id', $id)->first();

    if (!$status) {
        return response()->json(['error' => 'Status not found.'], 404);
    }

    $status->delete();

    return response()->json(['message' => 'Status deleted.']);
    }
}
