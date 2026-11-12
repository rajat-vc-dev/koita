<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageTemplateController extends Controller
{
    public function index(): View
    {
        $templates = PageTemplate::query()->orderBy('name')->get();

        return view('admin.page-templates.index', compact('templates'));
    }

    public function edit(PageTemplate $pageTemplate): View
    {
        return view('admin.page-templates.edit', [
            'template' => $pageTemplate,
        ]);
    }

    public function update(Request $request, PageTemplate $pageTemplate): RedirectResponse
    {
        $data = $request->validate([
            'page_title' => ['required', 'string', 'max:255'],
            'html' => ['required', 'string'],
        ]);

        $pageTemplate->update($data);

        return redirect()
            ->route('admin.page-templates.index')
            ->with('status', 'Thank you page template updated.');
    }
}
