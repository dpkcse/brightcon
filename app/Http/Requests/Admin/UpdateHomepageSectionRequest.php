<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;
class UpdateHomepageSectionRequest extends FormRequest { public function authorize(): bool { return true; } public function rules(): array { return ['title'=>['nullable','string','max:150'],'subtitle'=>['nullable','string','max:255'],'content'=>['nullable','string'],'image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],'button_text'=>['nullable','string','max:80'],'button_link'=>['nullable','string','max:255'],'background_color'=>['nullable','regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],'background_image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],'status'=>['nullable','boolean'],'sort_order'=>['nullable','integer','min:0']]; } }
