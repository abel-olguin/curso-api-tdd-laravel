<?php

namespace App\Http\Requests;

use App\Helpers\Base64Helper;
use App\Rules\Base64ImageRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePlateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'        => 'required',
            'price'       => 'required',
            'description' => 'required',
            'image' => ['required', new Base64ImageRule]
        ];
    }
}
