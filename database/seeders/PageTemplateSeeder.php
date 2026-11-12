<?php

namespace Database\Seeders;

use App\Models\PageTemplate;
use Illuminate\Database\Seeder;

class PageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => PageTemplate::INTEREST_YES,
                'name' => 'Thank you — Registration submit (Yes / eligible)',
                'page_title' => 'Thank You for Registering | Koita Foundation Tech Awards 2026',
                'html' => <<<'HTML'
<section class="pagehero thank-sec">
<div class="wrap">
<h1 style="margin-top:12px">Thank You for Registering!</h1>
<p>Hello {{contact_person_name}}, your registration for <strong>{{organisation_name}}</strong> has been successfully received.</p>
<p>Your access code is: <strong>{{access_code}}</strong>. Please keep it safe — you will need it when the application form opens.</p>
<p>We will share updates on your registered email address ({{email}}).</p>
</div>
</section>
HTML,
                'placeholders' => 'contact_person_name, organisation_name, email, access_code, mobile, website, designation',
            ],
            [
                'key' => PageTemplate::INTEREST_NO,
                'name' => 'Thank you — Registration submit (No)',
                'page_title' => 'Thank You for Registering | Koita Foundation Tech Awards 2026',
                'html' => <<<'HTML'
<section class="pagehero thank-sec">
<div class="wrap">
<h1 style="margin-top:12px">Thank You for Registering!</h1>
<p>Hello {{contact_person_name}}, thank you for registering your interest for <strong>{{organisation_name}}</strong>.</p>
<p>Your registration has been successfully received and is being reviewed.</p>
<p>We will share updates on your registered email address ({{email}}).</p>
</div>
</section>
HTML,
                'placeholders' => 'contact_person_name, organisation_name, email, mobile, website, designation',
            ],
            [
                'key' => PageTemplate::DOCUMENT_RESUBMIT,
                'name' => 'Thank you — Resubmit documents',
                'page_title' => 'Documents Updated | Koita Foundation Tech Awards 2026',
                'html' => <<<'HTML'
<section class="pagehero thank-sec">
<div class="wrap">
<h1 style="margin-top:12px">Documents Updated</h1>
<p>Hello {{contact_person_name}}, your documents for <strong>{{organisation_name}}</strong> have been updated successfully.</p>
<p>Our team will review the new files and contact you at {{email}} if anything else is needed.</p>
</div>
</section>
HTML,
                'placeholders' => 'contact_person_name, organisation_name, email',
            ],
            [
                'key' => PageTemplate::APPLICATION_SUBMIT,
                'name' => 'Thank you — Application submit',
                'page_title' => 'Application Submitted — AI for Impact Awards',
                'html' => <<<'HTML'
<section class="pagehero thank-sec">
<div class="wrap">
<h1 style="margin-top:12px">Application received</h1>
<p>Thank you, {{contact_person_name}}. Our editorial team will review your solution for <strong>{{organisation_name}}</strong> and be in touch about the next steps.</p>
</div>
</section>
HTML,
                'placeholders' => 'contact_person_name, organisation_name, email',
            ],
        ];

        foreach ($templates as $template) {
            PageTemplate::query()->updateOrCreate(
                ['key' => $template['key']],
                $template,
            );
        }
    }
}
