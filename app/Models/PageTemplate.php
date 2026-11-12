<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'name', 'page_title', 'html', 'placeholders'])]
class PageTemplate extends Model
{
    public const INTEREST_YES = 'thanks_interest_yes';

    public const INTEREST_NO = 'thanks_interest_no';

    public const DOCUMENT_RESUBMIT = 'thanks_document_resubmit';

    public const APPLICATION_SUBMIT = 'thanks_application_submit';

    /**
     * Replace {{placeholders}} in page_title/html.
     *
     * @param  array<string, string|null>  $data
     * @return array{page_title: string, html: string}
     */
    public function render(array $data): array
    {
        $replace = static function (string $text) use ($data): string {
            foreach ($data as $key => $value) {
                $text = str_replace('{{'.$key.'}}', (string) ($value ?? ''), $text);
            }

            return $text;
        };

        return [
            'page_title' => $replace($this->page_title),
            'html' => $replace($this->html),
        ];
    }
}
