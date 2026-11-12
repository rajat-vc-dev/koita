<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'applicant_id',
    'solution_name',
    'social_problem',
    'solution_description',
    'users_of_solution',
    'users_other',
    'technical_details',
    'demo_video_url',
    'testimonial_video_url',
])]
class ApplicantAiSolution extends Model
{
    protected function casts(): array
    {
        return [
            'users_of_solution' => 'array',
        ];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
