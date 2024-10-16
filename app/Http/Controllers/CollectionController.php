<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCollectionRequest;

class CollectionController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        $collections = $user->collections()->paginate(10);

        return response()->json([
            'error' => false,
            'message' => 'Collections retrieved successfully.',
            'data' => $collections,
        ]);
    }

    public function store(StoreCollectionRequest $request): JsonResponse
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        Collection::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'error' => false,
            'message' => 'Collection created successfully.',
            'data' => null,
        ]);
    }

    public function show(Collection $collection): JsonResponse
    {
        return response()->json([
            'error' => false,
            'message' => 'Collection retrieved successfully.',
            'data' => $collection,
        ]);
    }

    public function update(Collection $collection, StoreCollectionRequest $request): JsonResponse
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        if ($user->id != $collection->user_id) {
            return response()->json([
                'error' => true,
                'message' => 'You are not authorized to update this collection.',
                'data' => null,
            ]);
        }

        $collection->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'error' => false,
            'message' => 'Collection updated successfully.',
            'data' => null,
        ]);
    }

    public function destroy(Collection $collection): JsonResponse
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        if ($collection->user_id != $user->id) {
            return response()->json([
                'error' => true,
                'message' => 'You are not authorized to delete this collection.',
                'data' => null,
            ]);
        }
        $collection->delete();

        return response()->json([
            'error' => false,
            'message' => 'Collection deleted successfully.',
            'data' => null,
        ]);
    }
}
