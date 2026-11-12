<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BusinessEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email = strtolower(trim((string) $value));
        $domain = substr(strrchr($email, '@') ?: '', 1);

        if ($domain === '') {
            $fail('Please enter a valid business email address.');

            return;
        }

        $blocked = array_map('strtolower', config('koita.blocked_email_domains', []));

        if (in_array($domain, $blocked, true)) {
            $fail('Please use your organisation email or Gmail address. Other free email providers are not allowed.');
        }
    }
}
