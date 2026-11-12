<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'email',
    'otp_challenge',
    'otp_hash',
    'otp_expires_at',
    'otp_attempts',
    'verified_at',
    'marketing_consented_at',
    'ballot_submitted_at',
])]
#[Hidden(['otp_challenge', 'otp_hash'])]
class PeopleChoiceVoter extends Model
{
    protected function casts(): array
    {
        return [
            'otp_expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'marketing_consented_at' => 'datetime',
            'ballot_submitted_at' => 'datetime',
        ];
    }
}
