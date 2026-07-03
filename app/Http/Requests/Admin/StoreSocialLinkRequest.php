<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;
class StoreSocialLinkRequest extends FormRequest { public function authorize(): bool { return true; } public function rules(): array { return ['platform'=>['required','string','max:100'],'icon_class'=>['nullable','string','max:100'],'url'=>['nullable','url','max:255'],'sort_order'=>['nullable','integer','min:0'],'status'=>['nullable','boolean']]; } }
