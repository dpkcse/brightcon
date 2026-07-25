<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:180'], 'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'], 'website_url' => ['nullable', 'url:http,https', 'max:500'], 'is_active' => ['nullable', 'boolean'], 'is_featured' => ['nullable', 'boolean'], 'display_order' => ['nullable', 'integer', 'min:0']];
    }
}
