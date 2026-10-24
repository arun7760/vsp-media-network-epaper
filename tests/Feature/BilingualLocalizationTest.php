<?php

namespace Tests\Feature;

use App\Models\EpaperEdition;
use App\Models\EpaperRegion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BilingualLocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected EpaperRegion $region;

    protected EpaperEdition $edition;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->region = EpaperRegion::create([
            'name' => 'విశాఖపట్నం ఎడిషన్',
            'name_en' => 'Visakhapatnam Edition',
            'slug' => 'visakhapatnam',
            'order' => 1,
            'is_active' => true,
        ]);

        $this->edition = EpaperEdition::create([
            'region_id' => $this->region->id,
            'edition_date' => now()->toDateString(),
            'title' => 'విశాఖపట్నం సంచిక',
            'title_en' => 'Visakhapatnam Edition',
            'status' => 'published',
            'published_at' => now()->subMinute(),
            'total_pages' => 4,
            'created_by' => $this->admin->id,
            'scheduled_at' => now(),
        ]);
    }

    public function test_admin_editions_renders_correctly_in_english(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['locale' => 'en'])
            ->get('/admin/editions');

        $response->assertStatus(200);
        $response->assertSee('Manage Editions');
        $response->assertSee('All Editions');
        $response->assertSee('Upload New Edition');
        $response->assertSee('Visakhapatnam Edition');
        $response->assertSee('Pages');
        $response->assertSee('Editor');
        $response->assertDontSee('admin.col_date');
    }

    public function test_admin_editions_renders_correctly_in_telugu(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['locale' => 'te'])
            ->get('/admin/editions');

        $response->assertStatus(200);
        $response->assertSee('ఈ-పేపర్ సంచికల నిర్వహణ');
        $response->assertSee('విశాఖపట్నం ఎడిషన్');
        $response->assertSee('పేజీలు');
        $response->assertSee('ఎడిటర్');
        $response->assertDontSee('admin.col_date');
    }

    public function test_edition_editor_renders_in_english_and_telugu(): void
    {
        // English
        $responseEn = $this->actingAs($this->admin)
            ->withSession(['locale' => 'en'])
            ->get("/admin/editions/{$this->edition->id}/edit");

        $responseEn->assertStatus(200);
        $responseEn->assertSee('Edition Editor');
        $responseEn->assertSee('Page Gallery &amp; Order', false);
        $responseEn->assertSee('Save Changes');
        $responseEn->assertSee('View in Reader');
        $responseEn->assertSee('Visakhapatnam Edition');

        // Telugu
        $responseTe = $this->actingAs($this->admin)
            ->withSession(['locale' => 'te'])
            ->get("/admin/editions/{$this->edition->id}/edit");

        $responseTe->assertStatus(200);
        $responseTe->assertSee('సంచిక ఎడిటర్');
        $responseTe->assertSee('పేజీల జాబితా &amp; వరుస క్రమం', false);
        $responseTe->assertSee('మార్పులను భద్రపరచండి');
        $responseTe->assertSee('రీడర్‌లో చూడండి');
        $responseTe->assertSee('విశాఖపట్నం సంచిక');
    }

    public function test_locale_switching_route_updates_session(): void
    {
        $response = $this->get('/locale/en');
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');

        $responseTe = $this->get('/locale/te');
        $responseTe->assertRedirect();
        $responseTe->assertSessionHas('locale', 'te');
    }

    public function test_public_reader_renders_locale_properly(): void
    {
        $page = $this->edition->pages()->create([
            'page_number' => 1,
            'page_title' => 'Main Page',
            'image_path' => 'pages/1.jpg',
            'thumbnail_path' => 'pages/1_thumb.jpg',
        ]);

        // English public reader
        $responseEn = $this->withSession(['locale' => 'en'])
            ->get("/edition/{$this->edition->id}?page=1");

        $responseEn->assertStatus(200);
        $responseEn->assertSee('Home');
        $responseEn->assertSee('Page 1 / 1');
        $responseEn->assertSee('Pages (Filmstrip)');

        // Telugu public reader
        $responseTe = $this->withSession(['locale' => 'te'])
            ->get("/edition/{$this->edition->id}?page=1");

        $responseTe->assertStatus(200);
        $responseTe->assertSee('హోమ్');
        $responseTe->assertSee('పేజీ 1 / 1');
        $responseTe->assertSee('పేజీలు (Filmstrip)');
    }

    public function test_workflow_buttons_operate_correctly(): void
    {
        $draftEdition = EpaperEdition::create([
            'region_id' => $this->region->id,
            'edition_date' => now()->addDay()->toDateString(),
            'title' => 'టెస్ట్ డ్రాఫ్ట్ సంచిక',
            'status' => 'draft',
            'total_pages' => 1,
            'created_by' => $this->admin->id,
        ]);

        $draftEdition->pages()->create([
            'page_number' => 1,
            'image_path' => 'pages/1.jpg',
            'thumbnail_path' => 'pages/1_thumb.jpg',
        ]);

        // 1. Publish Now
        $publishResponse = $this->actingAs($this->admin)
            ->post("/admin/editions/{$draftEdition->id}/publish-now");
        $publishResponse->assertRedirect();
        $this->assertEquals('published', $draftEdition->fresh()->status);

        // 2. Unpublish (Revert to Draft)
        $unpublishResponse = $this->actingAs($this->admin)
            ->post("/admin/editions/{$draftEdition->id}/unpublish");
        $unpublishResponse->assertRedirect();
        $this->assertEquals('draft', $draftEdition->fresh()->status);

        // 3. Approve & Schedule
        $approveResponse = $this->actingAs($this->admin)
            ->post("/admin/editions/{$draftEdition->id}/approve", [
                'publish_action' => 'schedule_morning',
            ]);
        $approveResponse->assertRedirect();
        $this->assertEquals('scheduled', $draftEdition->fresh()->status);
    }

    public function test_editions_search_and_filter_portal_and_datatable_rendering(): void
    {
        // In English
        $responseEn = $this->actingAs($this->admin)
            ->withSession(['locale' => 'en'])
            ->get('/admin/editions');

        $responseEn->assertStatus(200);
        $responseEn->assertSee('Editions Search &amp; Filter Portal', false);
        $responseEn->assertSee('data-datatable="true"', false);
        $responseEn->assertSee('admin-datatable.css');
        $responseEn->assertSee('admin-datatable.js');

        // In Telugu
        $responseTe = $this->actingAs($this->admin)
            ->withSession(['locale' => 'te'])
            ->get('/admin/editions');

        $responseTe->assertStatus(200);
        $responseTe->assertSee('సంచికల శోధన &amp; ఫిల్టర్ పోర్టల్', false);
        $responseTe->assertSee('data-datatable="true"', false);
    }
}
