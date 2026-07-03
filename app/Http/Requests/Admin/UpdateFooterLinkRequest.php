<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;
class UpdateFooterLinkRequest extends FormRequest { public function authorize(): bool { return true; } public function rules(): array { return ['label'=>['required','string','max:100'],'url'=>['required','string','max:255'],'target'=>['required','in:_self,_blank'],'sort_order'=>['nullable','integer','min:0'],'status'=>['nullable','boolean']]; } }
