<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Actions\MediaUpload;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Actions\StoreDropboxMedia;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;

class MediaController extends Controller
{
    public function __construct(private MediaUpload $mediaUpload, private StoreDropboxMedia $storeDropboxMedia) {}

    public function index(): JsonResponse
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $media = $user->media()->with('collections')->paginate(10);

        return response()->json([
            'error' => false,
            'message' => 'Media retrieved successfully.',
            'data' => $media,
        ]);
    }

    public function store(StoreMediaRequest $request): JsonResponse
    {
        $user = Auth::user();

        if ($request->isDropboxUrl()) {
            $media = $this->storeDropboxMedia->execute($user, $request->validated());
        } else {
            $media = $this->mediaUpload->execute($user, $request->validated());
        }

        $media->collections()->sync($request->collection_ids);

        return response()->json([
            'error' => false,
            'message' => 'Media uploaded successfully.',
            'data' => null,
        ]);
    }

    public function update(UpdateMediaRequest $request, Media $media): JsonResponse
    {
        $media->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $media->collections()->sync($request->collection_ids);

        return response()->json([
            'error' => false,
            'message' => 'Media updated successfully.',
            'data' => '',
        ]);
    }

    public function assignCollections(Request $request, Media $media): JsonResponse
    {
        $request->validate([
            'collection_ids' => 'required|array',
            'collection_ids.*' => 'exists:collections,id',
        ]);

        $media->collections()->sync($request->collection_ids);

        return response()->json([
            'error' => false,
            'message' => 'Collections assigned successfully.',
            'data' => $media->collections,
        ]);
    }

    public function destroy(Media $media): JsonResponse
    {
        $media->collections()->detach();
        $media->delete();

        return response()->json([
            'error' => false,
            'message' => 'Media deleted successfully.',
        ]);
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'exists:media,id',
        ]);

        $mediaItems = Media::whereIn('id', $request->media_ids)->where('user_id', Auth::id())->get();

        foreach ($mediaItems as $media) {
            $media->collections()->detach();
            $media->delete();
        }

        return response()->json([
            'error' => false,
            'message' => 'Media deleted successfully.',
            'data' => null,
        ]);
    }
}
