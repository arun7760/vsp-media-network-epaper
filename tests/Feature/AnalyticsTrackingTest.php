<?php

namespace Tests\Feature;

use App\Models\EpaperEdition;
use App\Models\EpaperRegion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_locale_switch_works_without_auth(): void
    {
        $response = $this->get('/locale/en');
        $response->assertStatus(302);
        $this->assertEquals('en', session('locale'));

        $responseTe = $this->get('/locale/te');
        $responseTe->assertStatus(302);
        $this->assertEquals('te', session('locale'));
    }

    public function test_today_readership_api_returns_success_and_count(): void
    {
        $response = $this->getJson('/api/readership/today');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'today_readers',
                'today_views',
                'formatted_readers',
                'source',
            ]);
    }

    public function test_reader_event_tracking_endpoint(): void
    {
        $region = EpaperRegion::create([
            'name' => 'Visakhapatnam',
            'slug' => 'visakhapatnam',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $edition = EpaperEdition::create([
            'region_id' => $region->id,
            'title' => 'VSP Times Vizag Edition',
            'edition_date' => now()->toDateString(),
            'status' => 'published',
            'total_pages' => 8,
            'published_at' => now(),
        ]);

        $response = $this->postJson('/api/track/event', [
            'event_type' => 'edition_open',
            'edition_id' => $edition->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('epaper_reader_events', [
            'edition_id' => $edition->id,
            'event_type' => 'edition_open',
        ]);
    }

    public function test_admin_analytics_requires_authentication(): void
    {
        $response = $this->get('/admin/analytics');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_analytics_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['locale' => 'en'])
            ->get('/admin/analytics');

        $response->assertStatus(200)
            ->assertSee('Reporting Period')
            ->assertSee('Traffic Sources');
    }

    public function test_analytics_tags_remain_disabled_when_keys_are_null(): void
    {
        config([
            'analytics.gtm_container_id' => null,
            'analytics.ga4_measurement_id' => null,
            'analytics.google_ads_conversion_id' => null,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('googletagmanager.com/gtm.js');
        $response->assertDontSee('googletagmanager.com/gtag/js');
    }

    public function test_ga4_and_gtm_tags_render_properly_when_configured(): void
    {
        config([
            'analytics.ga4_measurement_id' => 'G-VHYRSKXX6D',
            'analytics.gtm_container_id' => 'GTM-VSP999',
            'analytics.cross_domains' => ['vspmedianetwork.com', 'epaper.vspmedianetwork.com'],
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('googletagmanager.com/gtag/js?id=G-VHYRSKXX6D', false);
        $response->assertSee("gtag('config', 'G-VHYRSKXX6D'", false);
        $response->assertSee("googletagmanager.com/gtm.js?id='+i+dl", false);
        $response->assertSee('googletagmanager.com/ns.html?id=GTM-VSP999', false);
        $response->assertSee('vspmedianetwork.com', false);
        $response->assertSee('window.vspAnalytics', false);
    }

    public function test_admin_dashboard_does_not_render_public_analytics_tags(): void
    {
        config([
            'analytics.ga4_measurement_id' => 'G-VHYRSKXX6D',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertDontSee('googletagmanager.com/gtag/js');
        $response->assertDontSee('G-VHYRSKXX6D');
    }
}
