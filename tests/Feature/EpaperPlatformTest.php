<?php

namespace Tests\Feature;

use App\Models\EpaperEdition;
use App\Models\EpaperPage;
use App\Models\EpaperRegion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
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

    public function test_scheduled_publication_command_publishes_approved_ready_editions(): void
    {
        $user = \App\Models\User::factory()->create();
        $tomorrow = Carbon::tomorrow();

        // Create an edition scheduled for tomorrow 5:00 AM IST
        $scheduled = EpaperEdition::create([
            'region_id' => $this->mainRegion->id,
            'edition_date' => $tomorrow,
            'title' => 'షెడ్యూల్డ్ ఉదయపు సంచిక',
            'total_pages' => 2,
            'status' => 'scheduled',
            'scheduled_at' => Carbon::now('Asia/Kolkata')->subMinutes(5), // time has arrived
            'approved_by' => $user->id,
        ]);

        EpaperPage::create([
            'edition_id' => $scheduled->id,
            'page_number' => 1,
            'page_title' => 'Page 1',
            'status' => 'ready',
        ]);

        $this->artisan('epaper:publish-scheduled')
            ->expectsOutputToContain('Published Edition ID '.$scheduled->id)
            ->assertExitCode(0);

        $scheduled->refresh();
        $this->assertEquals('published', $scheduled->status);
        $this->assertNotNull($scheduled->published_at);

        $this->assertDatabaseHas('epaper_workflow_logs', [
            'edition_id' => $scheduled->id,
            'action' => 'auto_publish',
            'from_status' => 'scheduled',
            'to_status' => 'published',
        ]);
    }

    public function test_scheduled_publication_skips_unapproved_or_pageless_editions(): void
    {
        $nextWeek = Carbon::today()->addDays(3);

        // 1. Unapproved edition
        $unapproved = EpaperEdition::create([
            'region_id' => $this->mainRegion->id,
            'edition_date' => $nextWeek,
            'title' => 'ఆమోదం లేని సంచిక',
            'total_pages' => 1,
            'status' => 'scheduled',
            'scheduled_at' => Carbon::now('Asia/Kolkata')->subMinutes(10),
            'approved_by' => null, // Not approved!
        ]);

        EpaperPage::create([
            'edition_id' => $unapproved->id,
            'page_number' => 1,
            'page_title' => 'Page 1',
            'status' => 'ready',
        ]);

        $this->artisan('epaper:publish-scheduled')
            ->expectsOutputToContain('Lacks editorial approval')
            ->assertExitCode(0);

        $unapproved->refresh();
        $this->assertEquals('scheduled', $unapproved->status);
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'editor@vsptimes.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'editor',
            'is_active' => true,
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'editor@vsptimes.com',
            'password' => 'Secret123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_editor_can_upload_pdf_edition_and_pages_extracted(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
        ]);

        $pdfContent = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R >>\nendobj\nxref\n0 4\n0000000000 65535 f\n0000000010 00000 n\n0000000060 00000 n\n0000000117 00000 n\ntrailer\n<< /Size 4 /Root 1 0 R >>\nstartxref\n168\n%%EOF";
        $file = UploadedFile::fake()->createWithContent('visakha_main.pdf', $pdfContent);

        $uploadDate = Carbon::today()->addDays(5)->toDateString();

        $response = $this->actingAs($editor)->post(route('admin.editions.store'), [
            'region_id' => $this->mainRegion->id,
            'edition_date' => $uploadDate,
            'title' => 'నూతన విశాఖ సంచిక',
            'volume_number' => 'సంపుటి 1',
            'issue_number' => 'సంచిక 1',
            'pdf_file' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('epaper_editions', [
            'region_id' => $this->mainRegion->id,
            'title' => 'నూతన విశాఖ సంచిక',
            'status' => 'draft',
            'created_by' => $editor->id,
        ]);

        $edition = EpaperEdition::where('title', 'నూతన విశాఖ సంచిక')->first();
        $response->assertRedirect(route('admin.editions.edit', $edition));
        $this->assertGreaterThan(0, $edition->pages()->count());
    }

    public function test_non_pdf_file_rejected_on_upload(): void
    {
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);
        $fakeText = UploadedFile::fake()->create('malicious.exe', 100);

        $response = $this->actingAs($editor)->post(route('admin.editions.store'), [
            'region_id' => $this->mainRegion->id,
            'edition_date' => Carbon::today()->addDays(6)->toDateString(),
            'title' => 'టెస్ట్ ఎడిషన్',
            'pdf_file' => $fakeText,
        ]);

        $response->assertSessionHasErrors(['pdf_file']);
    }

    public function test_duplicate_edition_date_and_region_prevented(): void
    {
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);

        // Attempt to upload for the same region and date as publishedEdition
        $pdfContent = "%PDF-1.4\n%EOF";
        $file = UploadedFile::fake()->createWithContent('dup.pdf', $pdfContent);

        $response = $this->actingAs($editor)->post(route('admin.editions.store'), [
            'region_id' => $this->publishedEdition->region_id,
            'edition_date' => $this->publishedEdition->edition_date->toDateString(),
            'title' => 'డూప్లికేట్ ఎడిషన్',
            'pdf_file' => $file,
        ]);

        $response->assertSessionHasErrors(['edition_date']);
    }

    public function test_role_permissions_reviewer_cannot_upload_edition(): void
    {
        $reviewer = User::factory()->create(['role' => 'reviewer', 'is_active' => true]);

        $response = $this->actingAs($reviewer)->get(route('admin.editions.create'));
        $response->assertStatus(403);
    }

    public function test_role_permissions_editor_cannot_approve_or_publish(): void
    {
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);

        $approveResponse = $this->actingAs($editor)->post(route('admin.editions.approve', $this->draftEdition));
        $approveResponse->assertStatus(403);

        $publishResponse = $this->actingAs($editor)->post(route('admin.editions.publish', $this->draftEdition));
        $publishResponse->assertStatus(403);
    }

    public function test_reviewer_can_approve_edition_and_schedule_for_morning(): void
    {
        $reviewer = User::factory()->create(['role' => 'reviewer', 'is_active' => true]);

        $response = $this->actingAs($reviewer)->post(route('admin.editions.approve', $this->draftEdition), [
            'publish_action' => 'schedule_morning',
            'notes' => 'Page layout verified, good to publish.',
        ]);

        $response->assertSessionHas('success');
        $this->draftEdition->refresh();
        $this->assertEquals('scheduled', $this->draftEdition->status);
        $this->assertEquals($reviewer->id, $this->draftEdition->approved_by);
        $this->assertNotNull($this->draftEdition->scheduled_at);
    }

    public function test_reviewer_can_reject_edition_with_reason(): void
    {
        $reviewer = User::factory()->create(['role' => 'reviewer', 'is_active' => true]);

        // Place edition in pending_approval
        $this->draftEdition->update(['status' => 'pending_approval']);

        $response = $this->actingAs($reviewer)->post(route('admin.editions.reject', $this->draftEdition), [
            'reason' => 'పేజీ 2 లో ఫాంట్ సరిగ్గా లేదు.',
        ]);

        $response->assertSessionHas('warning');
        $this->draftEdition->refresh();
        $this->assertEquals('draft', $this->draftEdition->status);

        $this->assertDatabaseHas('epaper_workflow_logs', [
            'edition_id' => $this->draftEdition->id,
            'action' => 'reject',
            'notes' => 'తిరస్కరించబడింది: పేజీ 2 లో ఫాంట్ సరిగ్గా లేదు.',
        ]);
    }

    public function test_reorder_pages_swaps_page_numbers(): void
    {
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);

        $page1 = EpaperPage::where('edition_id', $this->publishedEdition->id)->where('page_number', 1)->first();
        $page2 = EpaperPage::where('edition_id', $this->publishedEdition->id)->where('page_number', 2)->first();

        $this->assertEquals(1, $page1->page_number);
        $this->assertEquals(2, $page2->page_number);

        $response = $this->actingAs($editor)->post(route('admin.editions.reorder', $this->publishedEdition), [
            'page_id' => $page1->id,
            'direction' => 'down',
        ]);

        $response->assertSessionHas('success');

        $page1->refresh();
        $page2->refresh();

        $this->assertEquals(2, $page1->page_number);
        $this->assertEquals(1, $page2->page_number);
    }

    public function test_admin_can_publish_live_immediately(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        EpaperPage::create([
            'edition_id' => $this->draftEdition->id,
            'page_number' => 1,
            'page_title' => 'పేజీ 1',
            'status' => 'ready',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.editions.publish', $this->draftEdition));

        $response->assertSessionHas('success');
        $this->draftEdition->refresh();
        $this->assertEquals('published', $this->draftEdition->status);
        $this->assertNotNull($this->draftEdition->published_at);
    }

    public function test_locale_switching_toggles_language_in_session(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('admin.locale.switch', 'en'));
        $response->assertSessionHas('locale', 'en');

        $response = $this->actingAs($admin)->get(route('admin.locale.switch', 'te'));
        $response->assertSessionHas('locale', 'te');
    }

    public function test_admin_can_create_new_editorial_user_with_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Vijayawada Desk',
            'email' => 'vja.desk@vspmedianetwork.com',
            'role' => 'editor',
            'password' => 'Password123#',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'email' => 'vja.desk@vspmedianetwork.com',
            'role' => 'editor',
            'is_active' => true,
        ]);
    }

    public function test_editor_cannot_access_user_management(): void
    {
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);

        $response = $this->actingAs($editor)->get(route('admin.users.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_audit_logs(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('admin.audit-logs.index'));
        $response->assertStatus(200);
        $response->assertSee('ఎడిటోరియల్ ఆడిట్ లాగ్స్');
    }

    public function test_admin_can_update_publishing_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'site_name' => 'VSP TIMES Digital Newspaper',
            'timezone' => 'Asia/Kolkata',
            'default_publish_time' => '05:00',
            'max_pdf_size_mb' => 120,
            'auto_archive_days' => 730,
            'allow_public_downloads' => '1',
            'contact_email' => 'editorial@vspmedianetwork.com',
            'whatsapp_number' => '+91 99999 88888',
        ]);

        $response->assertSessionHas('success');
    }

    public function test_upload_and_processing_with_actual_vsp_times_newspaper_pdf(): void
    {
        $fixturePath = base_path('tests/fixtures/vsp_times_sample.pdf');
        $this->assertFileExists($fixturePath, 'Actual VSP Times PDF fixture must be available for testing.');

        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);

        // Create a copy to simulate uploaded file
        $tempPath = tempnam(sys_get_temp_dir(), 'vsp_test_') . '.pdf';
        copy($fixturePath, $tempPath);

        $uploadedFile = new UploadedFile($tempPath, 'vsp_times_edition_oct2026.pdf', 'application/pdf', null, true);

        $uploadDate = Carbon::today()->addDays(10)->toDateString();

        $response = $this->actingAs($editor)->post(route('admin.editions.store'), [
            'region_id' => $this->vskpRegion->id,
            'edition_date' => $uploadDate,
            'title' => 'విశాఖపట్నం అసలైన నమూనా ఈ-పేపర్ సంచిక',
            'volume_number' => 'సంపుటి 1',
            'issue_number' => 'సంచిక 105',
            'pdf_file' => $uploadedFile,
        ]);

        $response->assertSessionHasNoErrors();

        $edition = EpaperEdition::where('title', 'విశాఖపట్నం అసలైన నమూనా ఈ-పేపర్ సంచిక')->first();
        $this->assertNotNull($edition);
        $this->assertEquals('draft', $edition->status);
        $this->assertGreaterThan(0, $edition->total_pages);
        $this->assertEquals($edition->total_pages, $edition->pages()->count());

        // Cleanup
        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }

    public function test_unauthorized_roles_cannot_access_restricted_settings_or_audit_logs(): void
    {
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);
        $reviewer = User::factory()->create(['role' => 'reviewer', 'is_active' => true]);

        // Editor cannot access settings or audit logs
        $this->actingAs($editor)->get(route('admin.settings.index'))->assertStatus(403);
        $this->actingAs($editor)->get(route('admin.audit-logs.index'))->assertStatus(403);

        // Reviewer cannot access settings or audit logs
        $this->actingAs($reviewer)->get(route('admin.settings.index'))->assertStatus(403);
        $this->actingAs($reviewer)->get(route('admin.audit-logs.index'))->assertStatus(403);
    }

    public function test_inactive_user_is_logged_out_and_forbidden(): void
    {
        $inactiveAdmin = User::factory()->create(['role' => 'admin', 'is_active' => false]);

        $response = $this->actingAs($inactiveAdmin)->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_download_full_edition_pdf(): void
    {
        // Public download of published edition
        $response = $this->get(route('epaper.download', $this->publishedEdition));
        $this->assertTrue(in_array($response->status(), [200, 302], true));

        // Guest cannot download draft edition
        $draftResponse = $this->get(route('epaper.download', $this->draftEdition));
        $draftResponse->assertStatus(403);
    }

    public function test_duplicate_edition_date_upload_returns_validation_error_gracefully(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $pdfFile = \Illuminate\Http\UploadedFile::fake()->createWithContent('oct_edition.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF");

        // Try to upload an edition with the exact same region and date as $this->publishedEdition
        $response = $this->actingAs($admin)->post(route('admin.editions.store'), [
            'region_id' => $this->publishedEdition->region_id,
            'edition_date' => $this->publishedEdition->edition_date->toDateString(),
            'title' => 'Duplicate Edition Attempt',
            'pdf_file' => $pdfFile,
        ]);

        $response->assertSessionHasErrors(['edition_date']);
        $response->assertStatus(302);
    }

    public function test_uploading_previously_deleted_date_succeeds_without_unique_constraint_violation(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $pdfFile = \Illuminate\Http\UploadedFile::fake()->createWithContent('oct22_edition.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF");

        // First, soft delete the draft edition
        $dateToReuse = $this->draftEdition->edition_date->toDateString();
        $regionId = $this->draftEdition->region_id;
        $this->draftEdition->delete();

        // Now upload for the same deleted date
        $response = $this->actingAs($admin)->post(route('admin.editions.store'), [
            'region_id' => $regionId,
            'edition_date' => $dateToReuse,
            'title' => 'VSP Times Re-uploaded Edition',
            'pdf_file' => $pdfFile,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('epaper_editions', [
            'region_id' => $regionId,
            'title' => 'VSP Times Re-uploaded Edition',
            'deleted_at' => null,
        ]);
    }
}


