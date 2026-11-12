<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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

    #[Scope]
    protected function inDisplayOrder(Builder $query): void
    {
        $query->orderByRaw('CASE WHEN display_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('display_order')
            ->orderBy('title')
            ->orderBy('id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
