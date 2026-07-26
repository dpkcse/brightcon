<?php

namespace App\Http\Requests\Admin;

use App\Support\SafeCmsUrl;
use Illuminate\Foundation\Http\FormRequest;

class StoreMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['label' => ['required', 'string', 'max:100'], 'menu_location' => 'required|in:header,footer', 'parent_id' => 'nullable|exists:menu_items,id', 'link_type' => 'required|in:legacy,route,page,external', 'url' => SafeCmsUrl::rules(false), 'route_name' => 'nullable|required_if:link_type,route|string|max:100', 'page_id' => 'nullable|required_if:link_type,page|exists:pages,id', 'external_url' => ['nullable', 'required_if:link_type,external', ...SafeCmsUrl::rules(false)], 'target' => ['required', 'in:_self,_blank'], 'sort_order' => ['nullable', 'integer', 'min:0'], 'status' => ['nullable', 'boolean']];
    }
}
