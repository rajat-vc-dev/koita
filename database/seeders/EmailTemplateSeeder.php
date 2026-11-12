<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => EmailTemplate::USER_YES,
                'name' => 'User — Applicants registration (with documents)',
                'subject' => 'Registration received — your access code',
                'body' => "Hello {{contact_person_name}},\n\nThank you for registering for {{organisation_name}} ({{organisation_type}}).\n\nYour unique access code is: {{access_code}}\n\nPlease keep this code safe. You will need it when the application form opens.\n\nRegards,\nAI for Impact Awards",
                'placeholders' => 'contact_person_name, organisation_name, organisation_type, email, access_code, mobile, website, designation',
            ],
            [
                'key' => EmailTemplate::USER_NO,
                'name' => 'User — Applicants registration (incomplete documents)',
                'subject' => 'We received your applicants registration',
                'body' => "Hello {{contact_person_name}},\n\nThank you for registering your interest for {{organisation_name}}.\n\nOur team has received your details and will review your submission. We will contact you at {{email}} with next steps.\n\nRegards,\nAI for Impact Awards",
                'placeholders' => 'contact_person_name, organisation_name, organisation_type, email, mobile, website, designation',
            ],
            [
                'key' => EmailTemplate::ADMIN_YES,
                'name' => 'Admin — Applicants registration (with documents)',
                'subject' => 'New applicants registration — {{organisation_name}}',
                'body' => "A new applicants registration has been submitted.\n\nOrganisation: {{organisation_name}}\nType: {{organisation_type}}\nWebsite: {{website}}\nContact: {{contact_person_name}} ({{designation}})\nEmail: {{email}}\nMobile: {{mobile}}\nAccess code: {{access_code}}\n\nDocuments:\n{{documents_list}}",
                'placeholders' => 'organisation_name, organisation_type, website, contact_person_name, designation, email, mobile, access_code, documents_list',
            ],
            [
                'key' => EmailTemplate::ADMIN_NO,
                'name' => 'Admin — Applicants registration (incomplete documents)',
                'subject' => 'New applicants registration (incomplete) — {{organisation_name}}',
                'body' => "A new applicants registration has been submitted without complete documents.\n\nOrganisation: {{organisation_name}}\nType: {{organisation_type}}\nWebsite: {{website}}\nContact: {{contact_person_name}} ({{designation}})\nEmail: {{email}}\nMobile: {{mobile}}",
                'placeholders' => 'organisation_name, organisation_type, website, contact_person_name, designation, email, mobile',
            ],
            [
                'key' => EmailTemplate::USER_APPLY_INVITE,
                'name' => 'User — Complete your application invite',
                'subject' => 'Complete your AI for Impact Awards application',
                'body' => "Hello {{contact_person_name}},\n\nYou are invited to complete your application for {{organisation_name}}.\n\nUse this link: {{apply_url}}\nRegistered email: {{email}}\nAccess code: {{access_code}}\n\nRegards,\nAI for Impact Awards",
                'placeholders' => 'contact_person_name, organisation_name, email, access_code, apply_url',
            ],
            [
                'key' => EmailTemplate::USER_DOCUMENT_RESUBMIT,
                'name' => 'User — Resubmit registration documents',
                'subject' => 'Please update your registration details and documents',
                'body' => "Hello {{contact_person_name}},\n\nWe need you to update your registration details and documents for {{organisation_name}} ({{organisation_type}}).\n\nPlease use this secure link (valid for {{resubmit_valid_days}} days):\n{{resubmit_url}}\n\nYou can update organisation details (except email) and upload the required documents for your organisation type.\n\nRegards,\nAI for Impact Awards",
                'placeholders' => 'contact_person_name, organisation_name, organisation_type, email, resubmit_url, resubmit_valid_days',
            ],
            [
                'key' => EmailTemplate::USER_DOCUMENTS_RESUBMITTED,
                'name' => 'User — Documents resubmitted confirmation',
                'subject' => 'Documents received — {{organisation_name}}',
                'body' => "Hello {{contact_person_name}},\n\nThank you. We have received your updated registration documents for {{organisation_name}} ({{organisation_type}}).\n\nOur team will review them and contact you at {{email}} if anything else is needed.\n\nRegards,\nAI for Impact Awards",
                'placeholders' => 'contact_person_name, organisation_name, organisation_type, email',
            ],
            [
                'key' => EmailTemplate::ADMIN_DOCUMENTS_RESUBMITTED,
                'name' => 'Admin — Documents resubmitted',
                'subject' => 'Documents resubmitted — {{organisation_name}}',
                'body' => "An applicant has resubmitted registration documents.\n\nOrganisation: {{organisation_name}}\nType: {{organisation_type}}\nWebsite: {{website}}\nContact: {{contact_person_name}} ({{designation}})\nEmail: {{email}}\nMobile: {{mobile}}\nResubmitted at: {{resubmitted_at}}\n\nDocuments:\n{{documents_list}}\n\nView in admin: {{admin_applicant_url}}",
                'placeholders' => 'organisation_name, organisation_type, website, contact_person_name, designation, email, mobile, resubmitted_at, documents_list, admin_applicant_url',
            ],
            [
                'key' => EmailTemplate::USER_APPLICATION_SUBMIT,
                'name' => 'User — Application submitted',
                'subject' => 'Application received — {{organisation_name}}',
                'body' => "Hello {{contact_person_name}},\n\nThank you for submitting your application for {{organisation_name}}.\n\nSolution: {{solution_name}}\n\nOur editorial team will review your submission and be in touch at {{email}} about next steps.\n\nRegards,\nAI for Impact Awards",
                'placeholders' => 'contact_person_name, organisation_name, email, solution_name, submitted_at',
            ],
            [
                'key' => EmailTemplate::ADMIN_APPLICATION_SUBMIT,
                'name' => 'Admin — Application submitted',
                'subject' => 'New application submitted — {{organisation_name}}',
                'body' => "A full application has been submitted.\n\nOrganisation: {{organisation_name}}\nWebsite: {{website}}\nContact: {{contact_person_name}} ({{designation}})\nEmail: {{email}}\nMobile: {{mobile}}\nSolution: {{solution_name}}\nSubmitted at: {{submitted_at}}\n\nView in admin: {{admin_applicant_url}}",
                'placeholders' => 'organisation_name, website, contact_person_name, designation, email, mobile, solution_name, submitted_at, admin_applicant_url',
            ],
            [
                'key' => EmailTemplate::USER_APPLICATION_REJECTED,
                'name' => 'User — Application rejected',
                'subject' => 'Update on your AI for Impact Awards application',
                'body' => "Hello {{contact_person_name}},\n\nThank you for your interest in the AI for Impact Awards on behalf of {{organisation_name}}.\n\nAfter review, we are unable to proceed with your application at this time.\n\nIf you have questions, reply to this email or contact us using {{email}}.\n\nRegards,\nAI for Impact Awards",
                'placeholders' => 'contact_person_name, organisation_name, organisation_type, email',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::query()->updateOrCreate(
                ['key' => $template['key']],
                $template,
            );
        }
    }
}
