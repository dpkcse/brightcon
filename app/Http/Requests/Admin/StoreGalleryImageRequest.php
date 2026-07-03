<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;
class StoreGalleryImageRequest extends FormRequest { public function authorize(): bool { return true; } public function rules(): array { return ['title'=>['nullable','string','max:180'],'image'=>['required','image','mimes:jpg,jpeg,png,webp','max:4096'],'category'=>['nullable','string','max:100'],'sort_order'=>['nullable','integer','min:0'],'status'=>['nullable','boolean']]; } }
