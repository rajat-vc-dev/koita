<?php

return [
    'shortlist_enabled' => env('SHORTLIST_ENABLED', false),
    'admin_email' => env('ADMIN_EMAIL', 'admin@koita.test'),
    'access_code_prefix' => env('ACCESS_CODE_PREFIX', 'KF-'),
    'access_code_length' => (int) env('ACCESS_CODE_LENGTH', 10),
    'uploads_disk' => env('UPLOADS_DISK', 'local'),
    'document_resubmit_days' => max(1, (int) env('DOCUMENT_RESUBMIT_DAYS', 7)),

    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY', ''),
        'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
    ],

    /*
    | Decoy field name for basic honeypot spam protection on public forms.
    | Must stay empty; do not reuse a real form field name.
    */
    'honeypot' => [
        'field' => env('HONEYPOT_FIELD', 'fax'),
    ],

    /*
    | Free / consumer email domains blocked on public registration.
    */
    'blocked_email_domains' => [
        'googlemail.com',
        'yahoo.com',
        'yahoo.co.in',
        'yahoo.co.uk',
        'ymail.com',
        'hotmail.com',
        'hotmail.co.uk',
        'outlook.com',
        'live.com',
        'msn.com',
        'rediffmail.com',
        'aol.com',
        'icloud.com',
        'me.com',
        'mail.com',
        'gmx.com',
        'gmx.net',
        'protonmail.com',
        'proton.me',
        'zoho.com',
        'yandex.com',
        'yandex.ru',
    ],
];
