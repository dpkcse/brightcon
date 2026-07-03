<?php
namespace App\Http\Requests\Admin;
use App\Support\UniqueSlug; use Illuminate\Foundation\Http\FormRequest;
class StoreProjectCategoryRequest extends FormRequest { public function authorize(): bool { return true; } protected function prepareForValidation(): void { $this->merge(['slug'=>UniqueSlug::make('project_categories', $this->input('slug') ?: $this->input('name', 'category'))]); } public function rules(): array { return ['name'=>['required','string','max:150'],'slug'=>['nullable','string','max:180','unique:project_categories,slug'],'description'=>['nullable','string'],'sort_order'=>['nullable','integer','min:0'],'status'=>['nullable','boolean']]; } }
