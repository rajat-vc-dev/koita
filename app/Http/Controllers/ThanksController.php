<?php

namespace App\Http\Controllers;

use App\Models\PageTemplate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThanksController extends Controller
{
    public function __invoke(Request $request): View
    {
        /** @var array{key?: string, data?: array<string, string|null>}|null $payload */
        $payload = $request->session()->get('thank_you');

        $key = is_array($payload) ? ($payload['key'] ?? PageTemplate::INTEREST_YES) : PageTemplate::INTEREST_YES;
        $data = is_array($payload) ? ($payload['data'] ?? []) : [];

        $template = PageTemplate::query()->where('key', $key)->first()
            ?? PageTemplate::query()->where('key', PageTemplate::INTEREST_YES)->firstOrFail();

        $rendered = $template->render($data);

        return view('thanks', [
            'pageTitle' => $rendered['page_title'],
            'html' => $rendered['html'],
            'showSignOut' => false,
        ]);
    }
}
