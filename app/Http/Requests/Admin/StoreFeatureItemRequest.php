<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;
class StoreFeatureItemRequest extends FormRequest { public function authorize(): bool { return true; } public function rules(): array { return ['title'=>['required','string','max:150'],'short_text'=>['nullable','string','max:500'],'icon_class'=>['nullable','string','max:100'],'image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],'sort_order'=>['nullable','integer','min:0'],'status'=>['nullable','boolean']]; } }
