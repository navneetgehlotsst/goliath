<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class UniquePhoneNumberAndCountryCode implements Rule
{
    public function passes($attribute, $value)
    {
        // Logic to check if the phone number with country code is unique
        $count = User::where('phone_number', $value['phone_number'])
                     ->where('country_code', $value['country_code'])
                     ->count();
        return $count === 0;
    }

    public function message()
    {
        return 'The :attribute must be unique.';
    }
}
