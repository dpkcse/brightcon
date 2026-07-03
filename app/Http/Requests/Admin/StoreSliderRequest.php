<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;
class StoreSliderRequest extends FormRequest { public function authorize(): bool { return true; } public function rules(): array { return ['heading'=>['nullable','string','max:150'],'sub_heading'=>['nullable','string','max:150'],'description'=>['nullable','string','max:1000'],'image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],'button_text'=>['nullable','string','max:50'],'button_link'=>['nullable','string','max:255'],'sort_order'=>['nullable','integer','min:0'],'status'=>['nullable','boolean']]; } }
