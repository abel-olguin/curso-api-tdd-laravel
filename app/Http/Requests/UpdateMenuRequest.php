<?php

namespace App\Http\Requests;

use App\Rules\MenuPlateRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
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
            'plate_ids'   => 'required|array',
            'plate_ids.*' => ['required', 'exists:plates,id', new MenuPlateRule],
            'name'        => 'required',
            'description' => 'required',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'plate_ids' => array_unique($this->get('plate_ids') ?: []),
        ]);
    }
}
