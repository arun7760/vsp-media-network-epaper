<?php

namespace Database\Seeders;

use App\Models\EpaperEdition;
use App\Models\EpaperPage;
use App\Models\EpaperRegion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EpaperSampleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure an Admin user exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@vspmedianetwork.com'],
            [
                'name' => 'VSP Times Editorial Administrator',
                'password' => Hash::make('AdminVsp@2026#'),
            ]
        );

        // 2. Seed E-Paper Regions
        $regions = [
            ['name' => 'ప్రధాన సంచిక', 'name_en' => 'Main Edition', 'slug' => 'main', 'code' => 'MAIN', 'display_order' => 1],
            ['name' => 'విశాఖపట్నం ఎడిషన్', 'name_en' => 'Visakhapatnam Edition', 'slug' => 'visakhapatnam', 'code' => 'VSKP', 'display_order' => 2],
            ['name' => 'అమరావతి - విజయవాడ', 'name_en' => 'Amaravati & Vijayawada', 'slug' => 'amaravati', 'code' => 'AMR', 'display_order' => 3],
            ['name' => 'హైదరాబాద్ ఎడిషన్', 'name_en' => 'Hyderabad Edition', 'slug' => 'hyderabad', 'code' => 'HYD', 'display_order' => 4],
        ];

        $regionModels = [];
        foreach ($regions as $r) {
            $regionModels[$r['slug']] = EpaperRegion::firstOrCreate(['slug' => $r['slug']], $r);
        }

        $mainRegion = $regionModels['main'];
        $vskpRegion = $regionModels['visakhapatnam'];

        // 3. Seed Today's Main Edition
        $today = Carbon::today();
        $todayEdition = EpaperEdition::firstOrCreate([
            'region_id' => $mainRegion->id,
            'edition_date' => $today->toDateString(),
        ], [
            'edition_code' => $today->format('Y-m-d').'-MAIN',
            'title' => 'విఎస్పీ టైమ్స్ ప్రధాన సంచిక - '.$today->format('d M Y'),
            'title_en' => 'VSP Times Main Edition - '.$today->format('d M Y'),
            'volume_number' => 'సంపుటి 1',
            'issue_number' => 'సంచిక 108',
            'total_pages' => 8,
            'master_pdf_path' => 'storage/epaper/editions/sample-vsp-times.pdf',
            'master_pdf_size' => 12582912, // 12 MB
            'cover_image_path' => 'images/epaper/page-1.jpg',
            'cover_thumbnail_path' => 'images/epaper/page-1-thumb.jpg',
            'status' => 'published',
            'published_at' => $today->copy()->setTime(5, 0, 0),
            'views_count' => 1420,
            'downloads_count' => 310,
            'created_by' => $admin->id,
            'approved_by' => $admin->id,
            'meta_title' => 'VSP Times Telugu E-Paper Today | ప్రధాన సంచిక',
            'meta_description' => 'Read today\'s VSP Times Telugu newspaper digital edition with zoom and article clipping.',
        ]);

        $pageTitles = [
            1 => ['te' => 'ప్రధాన వార్తలు (Front Page)', 'en' => 'Main Headlines'],
            2 => ['te' => 'రాష్ట్రం & రాజకీయం', 'en' => 'State & Politics'],
            3 => ['te' => 'ఆంధ్రప్రదేశ్ ప్రత్యేకం', 'en' => 'AP Special'],
            4 => ['te' => 'వ్యాపారం & వాణిజ్యం', 'en' => 'Business & Finance'],
            5 => ['te' => 'సంపాదకీయం & విశ్లేషణ', 'en' => 'Editorial & Opinions'],
            6 => ['te' => 'జిల్లా సంచిక & సమాచారం', 'en' => 'District Pulse'],
            7 => ['te' => 'క్రీడలు & వినోదం', 'en' => 'Sports Arena'],
            8 => ['te' => 'సినిమా రంగం & జీవనశైలి', 'en' => 'Cinema & Lifestyle'],
        ];

        for ($p = 1; $p <= 8; $p++) {
            EpaperPage::firstOrCreate([
                'edition_id' => $todayEdition->id,
                'page_number' => $p,
            ], [
                'page_title' => $pageTitles[$p]['te'],
                'page_title_en' => $pageTitles[$p]['en'],
                'image_path' => "images/epaper/page-{$p}.jpg",
                'thumbnail_path' => "images/epaper/page-{$p}-thumb.jpg",
                'width' => 2480,
                'height' => 3508,
                'file_size' => 1450000,
                'status' => 'ready',
                'sort_order' => $p,
            ]);
        }

        // 4. Seed Today's Visakhapatnam Edition
        EpaperEdition::firstOrCreate([
            'region_id' => $vskpRegion->id,
            'edition_date' => $today->toDateString(),
        ], [
            'edition_code' => $today->format('Y-m-d').'-VSKP',
            'title' => 'విఎస్పీ టైమ్స్ విశాఖపట్నం సంచిక - '.$today->format('d M Y'),
            'title_en' => 'VSP Times Visakhapatnam Edition - '.$today->format('d M Y'),
            'volume_number' => 'సంపుటి 1',
            'issue_number' => 'సంచిక 108',
            'total_pages' => 6,
            'cover_image_path' => 'images/epaper/page-1.jpg',
            'status' => 'published',
            'published_at' => $today->copy()->setTime(5, 15, 0),
            'views_count' => 980,
            'downloads_count' => 180,
            'created_by' => $admin->id,
            'approved_by' => $admin->id,
        ]);

        // 5. Seed Yesterday's Edition for Archive
        $yesterday = Carbon::yesterday();
        $yesterdayEdition = EpaperEdition::firstOrCreate([
            'region_id' => $mainRegion->id,
            'edition_date' => $yesterday->toDateString(),
        ], [
            'edition_code' => $yesterday->format('Y-m-d').'-MAIN',
            'title' => 'విఎస్పీ టైమ్స్ ప్రధాన సంచిక - '.$yesterday->format('d M Y'),
            'title_en' => 'VSP Times Main Edition - '.$yesterday->format('d M Y'),
            'volume_number' => 'సంపుటి 1',
            'issue_number' => 'సంచిక 107',
            'total_pages' => 6,
            'cover_image_path' => 'images/epaper/page-1.jpg',
            'status' => 'published',
            'published_at' => $yesterday->copy()->setTime(5, 0, 0),
            'views_count' => 3890,
            'downloads_count' => 840,
            'created_by' => $admin->id,
            'approved_by' => $admin->id,
        ]);

        for ($p = 1; $p <= 6; $p++) {
            EpaperPage::firstOrCreate([
                'edition_id' => $yesterdayEdition->id,
                'page_number' => $p,
            ], [
                'page_title' => $pageTitles[$p]['te'] ?? "పేజీ {$p}",
                'page_title_en' => $pageTitles[$p]['en'] ?? "Page {$p}",
                'image_path' => "images/epaper/page-{$p}.jpg",
                'status' => 'ready',
                'sort_order' => $p,
            ]);
        }
    }
}
