<?php

namespace App\Rules;

use App\Helpers\Base64Helper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Base64ImageRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(!$value) return;

        $data = Base64Helper::getDataImage($value);
        if(empty($data)) {
            $fail('Image not valid');
        }
    }
}
