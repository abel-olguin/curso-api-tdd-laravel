<?php

namespace App\Rules;

use App\Models\Plate;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MenuPlateRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = Plate::where('id', $value)->where('restaurant_id', request()->restaurant->id)->exists();
        if (!$exists) {
            $fail('Este platillo no te pertence');
        }
    }
}
