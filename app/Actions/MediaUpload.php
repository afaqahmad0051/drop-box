<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Media;
use App\Enums\MediaType;
use Illuminate\Support\Facades\Storage;

class MediaUpload
{
    public function execute(User $user, array $mediaDetails): Media
    {
        $file = $mediaDetails['media'];
        $originalName = $file->getClientOriginalName();
        $safeName = str_replace(' ', '_', $originalName);
        $mimeType = $file->getMimeType();
        $mediaType = $this->detectMediaType($mimeType);
        $filePath = $file->storeAs("{$user->id}", $safeName, 'public');
        $mediaUrl = env('APP_URL').Storage::url($filePath);

        $media = Media::create([
            'user_id' => $user->id,
            'name' => $mediaDetails['name'],
            'media' => $mediaUrl,
            'description' => $mediaDetails['description'],
            'type' => $mediaType->value,
        ]);

        return $media;
    }

    private function detectMediaType(string $mimeType): MediaType
    {
        if (str_starts_with($mimeType, 'image/')) {
            return MediaType::Image;
        } elseif (str_starts_with($mimeType, 'video/')) {
            return MediaType::Video;
        }

        throw new \Exception('Unsupported media type.');
    }
}
