<?php

namespace App\Support;

final class Honeypot
{
    public static function field(): string
    {
        return (string) config('koita.honeypot.field', 'fax');
    }

    /**
     * @return array<string, list<string>>
     */
    public static function rules(): array
    {
        return [
            self::field() => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            self::field().'.prohibited' => 'Unable to submit the form. Please try again.',
        ];
    }
}
