<?php
namespace App\Http\Requests\Admin;
use App\Support\SafeCmsUrl;
use Illuminate\Foundation\Http\FormRequest;
class StoreMenuItemRequest extends FormRequest { public function authorize(): bool { return true; } public function rules(): array { return ['label'=>['required','string','max:100'],'url'=>SafeCmsUrl::rules(true),'target'=>['required','in:_self,_blank'],'sort_order'=>['nullable','integer','min:0'],'status'=>['nullable','boolean']]; } }
