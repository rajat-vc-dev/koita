<?php

namespace App\Http\Requests\Apply;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOrganizationDetailRequest extends FormRequest
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
            'primary_areas' => ['required', 'array', 'min:1'],
            'primary_areas.*' => ['string', 'max:100'],
            'primary_area_other' => ['nullable', 'string', 'max:255'],
            'mission_overview' => ['required', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $areas = $this->input('primary_areas', []);
            if (in_array('Other', $areas, true) && blank($this->input('primary_area_other'))) {
                $validator->errors()->add('primary_area_other', 'Please specify the other area of work.');
            }
        });
    }
}
