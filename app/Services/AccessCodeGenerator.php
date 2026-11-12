<?php

namespace App\Services;

use App\Models\Applicant;

class AccessCodeGenerator
{
    public function generate(): string
    {
        $prefix = (string) config('koita.access_code_prefix', 'KF-');
        $length = max(8, (int) config('koita.access_code_length', 10));
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        do {
            $suffix = '';
            for ($i = 0; $i < $length; $i++) {
                $suffix .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $code = $prefix.$suffix;
        } while (Applicant::query()->where('access_code', $code)->exists());

        return $code;
    }
}
