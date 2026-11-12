<?php

namespace App\Http\Requests\Apply;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAiSolutionRequest extends FormRequest
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
            'solution_name' => ['required', 'string', 'max:255'],
            'social_problem' => ['required', 'string'],
            'solution_description' => ['required', 'string'],
            'users_of_solution' => ['required', 'array', 'min:1'],
            'users_of_solution.*' => ['string', 'max:100'],
            'users_other' => ['nullable', 'string', 'max:255'],
            'technical_details' => ['required', 'string'],
            'demo_video_url' => ['required', 'url', 'max:500'],
            'testimonial_video_url' => ['required', 'url', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['demo_video_url', 'testimonial_video_url'] as $field) {
            $value = trim((string) $this->input($field));
            if ($value !== '' && ! preg_match('#^https?://#i', $value)) {
                $this->merge([$field => 'https://'.$value]);
            }
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $users = $this->input('users_of_solution', []);
            if (in_array('Other', $users, true) && blank($this->input('users_other'))) {
                $validator->errors()->add('users_other', 'Please specify the other users of the solution.');
            }
        });
    }
}
