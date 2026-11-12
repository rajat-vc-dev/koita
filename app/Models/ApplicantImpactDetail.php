<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'applicant_id',
    'adoption_stage',
    'user_count',
    'success_measurement',
    'outcomes_impact',
    'deck_path',
    'brand_asset_url',
    'consent',
])]
class ApplicantImpactDetail extends Model
{
    protected function casts(): array
    {
        return [
            'consent' => 'boolean',
            'user_count' => 'integer',
        ];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function deckUrl(): ?string
    {
        return $this->deck_path
            ? route('admin.applicants.files.show', [
                'applicant' => $this->applicant_id,
                'type' => 'deck',
            ])
            : null;
    }
}
