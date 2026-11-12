<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'applicant_id',
    'organization_name',
    'primary_areas',
    'primary_area_other',
    'mission_overview',
])]
class ApplicantOrganizationDetail extends Model
{
    protected function casts(): array
    {
        return [
            'primary_areas' => 'array',
        ];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
