<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreStatusRequest;
use App\Models\Status;
use Illuminate\Http\Request;
use App\Http\Services\StatusService;

class StatusController extends Controller
{
    public function __construct(private StatusService $statusService) {}

    public function index(Request $request, string $status)
    {
        $user = $request->user;
    $statusType = $request->route('status');
        $data = $this->statusService->index($user, $statusType);

        return response()->json(['data' => $data]);
    }

    public function store(StoreStatusRequest $request, string $id)
    {
         $user = request()->user;
        $data = $this->statusService->store($user, $id, $request->status);

        return response()->json(['message' => 'Status updated.', 'data' => $data]);
    }

    public function destroy(string $id, Request $request)
    {
        $user = $request->user;
        $data = $this->statusService->destroy($user, $id);

        if (! $data) {
            return response()->json(['error' => 'Status not found.'], 404);
        }

        return response()->json(['message' => 'Status deleted.']);
    }
}