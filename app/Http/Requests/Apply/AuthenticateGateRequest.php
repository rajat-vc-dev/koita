<?php

namespace App\Http\Requests\Apply;

use App\Rules\RecaptchaV2;
use App\Support\Honeypot;
use Illuminate\Foundation\Http\FormRequest;

class AuthenticateGateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'access_code' => ['required', 'string'],
            'g-recaptcha-response' => ['nullable', 'string', new RecaptchaV2],
            ...Honeypot::rules(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return Honeypot::messages();
    }
}
