<?php

namespace Tests\Feature\Admin;

use App\Models\PageTemplate;
use App\Models\User;
use Database\Seeders\PageTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTemplateAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PageTemplateSeeder::class);
    }

    public function test_superadmin_can_list_and_update_thank_you_template(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $template = PageTemplate::query()->where('key', PageTemplate::INTEREST_YES)->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.page-templates.index'))
            ->assertOk()
            ->assertSee('Thank you page templates')
            ->assertSee($template->name);

        $this->actingAs($admin)
            ->put(route('admin.page-templates.update', $template), [
                'page_title' => 'Custom Yes Title',
                'html' => '<section class="pagehero thank-sec"><div class="wrap"><h1>Custom Yes Thank You</h1></div></section>',
            ])
            ->assertRedirect(route('admin.page-templates.index'));

        $this->assertDatabaseHas('page_templates', [
            'key' => PageTemplate::INTEREST_YES,
            'page_title' => 'Custom Yes Title',
        ]);

        $this->assertStringContainsString('Custom Yes Thank You', $template->fresh()->html);
    }
}
