<?php

namespace App\Http\Requests\Apply;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreImpactDetailRequest extends FormRequest
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
            'adoption_stage' => ['required', 'in:dev,pilot,scale'],
            'user_count' => ['nullable', 'integer', 'min:1'],
            'success_measurement' => ['required', 'string'],
            'outcomes_impact' => ['required', 'string'],
            'deck' => ['required', 'file', 'mimes:pdf,ppt,pptx', 'max:20480'],
            'brand_asset_url' => ['required', 'string', 'max:2048', 'url'],
            'consent' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'deck.required' => 'Please upload your PPT / PDF.',
            'deck.mimes' => 'Only PDF, PPT, or PPTX files are allowed.',
            'deck.max' => 'Deck must be 20MB or less.',
            'brand_asset_url.required' => 'Please enter the brand asset URL.',
            'brand_asset_url.url' => 'Please enter a valid brand asset URL.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $url = trim((string) $this->input('brand_asset_url'));
        if ($url !== '' && ! preg_match('#^https?://#i', $url)) {
            $this->merge(['brand_asset_url' => 'https://'.$url]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $stage = $this->input('adoption_stage');
            if (in_array($stage, ['pilot', 'scale'], true) && blank($this->input('user_count'))) {
                $validator->errors()->add('user_count', 'Please enter the number of users.');
            }
        });
    }
}
