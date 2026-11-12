<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'name', 'subject', 'body', 'placeholders'])]
class EmailTemplate extends Model
{
    public const USER_YES = 'user_interest_yes';

    public const USER_NO = 'user_interest_no';

    public const ADMIN_YES = 'admin_interest_yes';

    public const ADMIN_NO = 'admin_interest_no';

    public const USER_APPLY_INVITE = 'user_apply_invite';

    public const USER_DOCUMENT_RESUBMIT = 'user_document_resubmit';

    public const USER_DOCUMENTS_RESUBMITTED = 'user_documents_resubmitted';

    public const ADMIN_DOCUMENTS_RESUBMITTED = 'admin_documents_resubmitted';

    public const USER_APPLICATION_SUBMIT = 'user_application_submit';

    public const ADMIN_APPLICATION_SUBMIT = 'admin_application_submit';

    public const USER_APPLICATION_REJECTED = 'user_application_rejected';

    public const USER_PEOPLE_CHOICE_OTP = 'user_people_choice_otp';

    public const USER_PEOPLE_CHOICE_CONFIRMATION = 'user_people_choice_confirmation';

    public const ADMIN_PEOPLE_CHOICE_BALLOT = 'admin_people_choice_ballot';

    /**
     * Replace {{placeholders}} in subject/body.
     *
     * @param  array<string, string|null>  $data
     * @return array{subject: string, body: string}
     */
    public function render(array $data, bool $asHtml = false): array
    {
        $subject = $this->replacePlaceholders($this->subject, $data, escape: false);

        if (! $asHtml) {
            return [
                'subject' => $subject,
                'body' => $this->replacePlaceholders($this->body, $data, escape: false),
            ];
        }

        if ($this->looksLikeHtml()) {
            $body = $this->replacePlaceholders($this->body, $data, escape: true, nl2br: true);
        } else {
            $body = nl2br(e($this->replacePlaceholders($this->body, $data, escape: false)), false);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    public function looksLikeHtml(): bool
    {
        return (bool) preg_match('/<(?:table|tr|td|div|p|br|center|html|body|img|a|span|h[1-6]|strong|em|ul|ol|li)\b/i', $this->body);
    }

    /**
     * @param  array<string, string|null>  $data
     */
    private function replacePlaceholders(string $text, array $data, bool $escape, bool $nl2br = false): string
    {
        foreach ($data as $key => $value) {
            $replacement = (string) ($value ?? '');
            if ($escape) {
                $replacement = e($replacement);
                if ($nl2br) {
                    $replacement = nl2br($replacement, false);
                }
            }
            $text = str_replace('{{'.$key.'}}', $replacement, $text);
        }

        return $text;
    }
}
