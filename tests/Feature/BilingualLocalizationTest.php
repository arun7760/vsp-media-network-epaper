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
}
