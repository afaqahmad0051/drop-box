<?php

namespace App\Http\Controllers;

use App\Actions\MediaUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreMediaRequest;

class MediaController extends Controller
{
    public function __construct(private MediaUpload $mediaUpload) {}

    public function index(): JsonResponse
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $media = $user->media()->paginate(10);

        return response()->json([
            'error' => false,
            'message' => 'Media retrieved successfully.',
            'data' => $media,
        ]);
    }

    public function store(StoreMediaRequest $request): JsonResponse
    {
        $mediaDetails = [
            'name' => $request->name,
            'media' => $request->media,
            'description' => $request->description,
        ];
        $user = Auth::user();

        $media = $this->mediaUpload->execute($user, $mediaDetails);

        return response()->json([
            'error' => false,
            'message' => 'Media uploaded successfully.',
            'data' => null,
        ]);
    }
}
