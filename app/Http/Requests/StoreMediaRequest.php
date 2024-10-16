<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'dropbox_url' => 'required_without:media|nullable|url|regex:/^https?:\/\/(www\.)?dropbox\.com\//',
            'media' => 'required_without:dropbox_url|nullable|mimes:jpg,jpeg,png,mp4,mov,webm',
            'description' => 'nullable|string',
            'collection_ids' => 'nullable|array',
            'collection_ids.*' => 'exists:collections,id',
        ];
    }

    public function messages(): array
    {
        return [
            'media_url.required_without' => 'Please provide a Dropbox URL or upload a media file.',
            'media_file.required_without' => 'Please upload a media file or provide a Dropbox URL.',
            'media_url.regex' => 'The URL must be a valid Dropbox link.',
        ];
    }

    public function isDropboxUrl(): bool
    {
        return $this->filled('dropbox_url');
    }
}
