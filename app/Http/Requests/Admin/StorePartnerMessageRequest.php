<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePartnerMessageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'], 'designation' => ['required', 'string', 'max:150'], 'organization' => ['nullable', 'string', 'max:150'],
            'image_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], 'organization_logo_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'short_message' => ['nullable', 'string', 'max:500'], 'full_message' => ['required', 'string', 'max:8000'], 'highlighted_text' => ['nullable', 'string', 'max:500'],
            'linkedin_url' => ['nullable', 'url', 'max:255'], 'display_order' => ['nullable', 'integer', 'min:0'], 'is_featured' => ['nullable', 'boolean'], 'is_active' => ['nullable', 'boolean'], 'published_at' => ['nullable', 'date'],
        ];
    }
}
