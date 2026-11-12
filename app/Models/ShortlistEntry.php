<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'applicant_id',
    'title',
    'solution_name',
    'sector',
    'tagline',
    'problem',
    'solution',
    'impact',
    'demo_url',
    'testimonial_url',
    'pdf_url',
    'cta_label',
    'cta_url',
    'logo_path',
    'is_enabled',
])]
class ShortlistEntry extends Model
{
    protected function casts(): array
    {
        return ['is_enabled' => 'boolean'];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
