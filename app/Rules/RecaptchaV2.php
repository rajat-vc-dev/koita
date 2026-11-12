<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaV2 implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $secret = (string) config('koita.recaptcha.secret_key');

        if ($secret === '') {
            if (app()->isProduction()) {
                $fail('reCAPTCHA is not configured. Please contact the administrator.');
            }

            return;
        }

        $token = is_string($value) ? trim($value) : '';

        if ($token === '') {
            $fail('Please complete the reCAPTCHA verification.');

            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => request()->ip(),
                ]);
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA request failed', [
                'message' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);
            $fail('Unable to verify reCAPTCHA. Please try again.');

            return;
        }

        $payload = $response->json() ?? [];
        $success = ($payload['success'] ?? false) === true;

        if (! $response->successful() || ! $success) {
            Log::warning('reCAPTCHA verification failed', [
                'http_status' => $response->status(),
                'success' => $payload['success'] ?? null,
                'error_codes' => $payload['error-codes'] ?? [],
                'hostname' => $payload['hostname'] ?? null,
                'ip' => request()->ip(),
                'has_token' => $token !== '',
            ]);

            $codes = $payload['error-codes'] ?? [];
            $detail = is_array($codes) && $codes !== []
                ? ' ('.implode(', ', $codes).')'
                : '';

            // Show Google codes only outside production (safer for users).
            if (! app()->isProduction() && $detail !== '') {
                $fail('reCAPTCHA verification failed'.$detail.'. Please try again.');
            } else {
                $fail('reCAPTCHA verification failed. Please try again.');
            }
        }
    }
}
