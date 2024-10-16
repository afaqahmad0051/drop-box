<?php

namespace App\Actions;

use Exception;
use App\Models\User;
use App\Models\Media;
use App\Enums\MediaType;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class StoreDropboxMedia
{
    public function execute(User $user, array $mediaDetails): Media
    {
        $dropboxUrl = $this->convertToDirectDownload($mediaDetails['dropbox_url']);
        $fileContent = $this->downloadMedia($dropboxUrl);
        $fileExtension = $this->extractFileExtension($dropboxUrl);

        $parsedUrl = parse_url($dropboxUrl);
        if ($parsedUrl === false || ! isset($parsedUrl['path'])) {
            throw new Exception('Invalid Dropbox URL.');
        }
        $path = $parsedUrl['path'];
        $safeName = basename($path);
        $mediaType = $this->detectMediaType($fileExtension);

        $filePath = Storage::disk('public')->put("{$user->id}/{$safeName}", $fileContent);

        // @phpstan-ignore argument.type
        $mediaUrl = env('APP_URL').'/'.Storage::url($filePath).'/'.$safeName;

        return Media::create([
            'user_id' => $user->id,
            'name' => $mediaDetails['name'],
            'media' => $mediaUrl,
            'description' => $mediaDetails['description'],
            'type' => $mediaType->value,
        ]);
    }

    private function convertToDirectDownload(string $url): string
    {
        return str_replace('dl=0', 'dl=1', $url);
    }

    private function downloadMedia(string $url): string
    {
        $response = Http::withOptions(['stream' => true])->get($url);

        if ($response->failed()) {
            throw new Exception('Failed to download media from Dropbox.');
        }

        return $response->body();
    }

    private function extractFileExtension(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (! $path) {
            throw new \InvalidArgumentException('Invalid URL provided.');
        }

        return pathinfo($path, PATHINFO_EXTENSION);
    }

    private function detectMediaType(string $extension): MediaType
    {
        $extension = strtolower($extension);

        return match ($extension) {
            'jpg', 'jpeg', 'png' => MediaType::Image,
            'mp4', 'mov', 'webm' => MediaType::Video,
            default => throw new Exception('Unsupported media type.'),
        };
    }
}
