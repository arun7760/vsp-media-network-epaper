<?php

namespace Tests\Feature;

use App\Models\EpaperEdition;
use App\Models\EpaperPage;
use App\Models\EpaperRegion;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpaperPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected EpaperRegion $mainRegion;

    protected EpaperRegion $vskpRegion;

    protected EpaperEdition $publishedEdition;

    protected EpaperEdition $draftEdition;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mainRegion = EpaperRegion::create([
            'name' => 'ప్రధాన సంచిక',
            'name_en' => 'Main Edition',
            'slug' => 'main',
            'code' => 'MAIN',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $this->vskpRegion = EpaperRegion::create([
            'name' => 'విశాఖపట్నం ఎడిషన్',
            'name_en' => 'Visakhapatnam Edition',
            'slug' => 'visakhapatnam',
            'code' => 'VSKP',
            'display_order' => 2,
            'is_active' => true,
        ]);

        $today = Carbon::today();

        // Published Edition with 4 pages
        $this->publishedEdition = EpaperEdition::create([
            'region_id' => $this->mainRegion->id,
            'edition_date' => $today->toDateString(),
            'edition_code' => $today->format('Y-m-d').'-MAIN',
            'title' => 'విఎస్పీ టైమ్స్ ప్రధాన సంచిక',
            'title_en' => 'VSP Times Main Edition',
            'volume_number' => 'సంపుటి 1',
            'issue_number' => 'సంచిక 101',
            'total_pages' => 4,
            'status' => 'published',
            'published_at' => $today->copy()->setTime(5, 0, 0),
            'views_count' => 50,
        ]);

        for ($i = 1; $i <= 4; $i++) {
            EpaperPage::create([
                'edition_id' => $this->publishedEdition->id,
                'page_number' => $i,
                'page_title' => "పేజీ {$i} ముఖ్యాంశాలు",
                'image_path' => "images/epaper/page-{$i}.jpg",
                'status' => 'ready',
                'sort_order' => $i,
            ]);
        }

        // Draft Edition (Unpublished)
        $this->draftEdition = EpaperEdition::create([
            'region_id' => $this->vskpRegion->id,
            'edition_date' => $today->toDateString(),
            'title' => 'డ్రాఫ్ట్ విశాఖపట్నం ఎడిషన్',
            'status' => 'draft',
            'total_pages' => 1,
        ]);
    }

    public function test_epaper_homepage_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('VSP TIMES ఈ-పేపర్');
        $response->assertSee('ప్రధాన సంచిక');
        $response->assertSee('ఇప్పుడే చదవండి');
    }

    public function test_reader_loads_page_one_and_increments_views(): void
    {
        $initialViews = $this->publishedEdition->views_count;

        $response = $this->get(route('epaper.reader', ['edition' => $this->publishedEdition, 'pageNumber' => 1]));

        $response->assertStatus(200);
        $response->assertSee('పేజీ 1 / 4');
        $response->assertSee('పేజీ 1 ముఖ్యాంశాలు');

        $this->publishedEdition->refresh();
        $this->assertEquals($initialViews + 1, $this->publishedEdition->views_count);
    }

    public function test_reader_loads_specified_page_number(): void
    {
        $response = $this->get(route('epaper.reader', ['edition' => $this->publishedEdition, 'pageNumber' => 3]));

        $response->assertStatus(200);
        $response->assertSee('పేజీ 3 / 4');
        $response->assertSee('పేజీ 3 ముఖ్యాంశాలు');
    }

    public function test_unpublished_draft_edition_is_forbidden_for_guests(): void
    {
        $response = $this->get(route('epaper.reader', ['edition' => $this->draftEdition, 'pageNumber' => 1]));

        $response->assertStatus(404);
    }

    public function test_archive_page_renders_and_filters_editions(): void
    {
        $response = $this->get(route('epaper.archive', [
            'region_id' => $this->mainRegion->id,
            'month' => Carbon::now()->month,
            'year' => Carbon::now()->year,
        ]));

        $response->assertStatus(200);
        $response->assertSee('VSP TIMES ఈ-పేపర్ ఆర్కైవ్');
        $response->assertSee($this->publishedEdition->title);
    }

    public function test_edition_workflow_logging_records_action(): void
    {
        $this->publishedEdition->recordWorkflow('submitted_review', 'draft', 'pending_approval', 'Ready for editorial check');

        $this->assertDatabaseHas('epaper_workflow_logs', [
            'edition_id' => $this->publishedEdition->id,
            'action' => 'submitted_review',
            'from_status' => 'draft',
            'to_status' => 'pending_approval',
            'notes' => 'Ready for editorial check',
        ]);
    }
}
