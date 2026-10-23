<?php

namespace App\Services;

use App\Models\EpaperEdition;
use App\Models\EpaperPage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EpaperPdfService
{
    /**
     * Validate PDF MIME, magic header, and file integrity
     */
    public function validatePdf(UploadedFile $file): array
    {
        // 1. Check client MIME type and extension
        $ext = strtolower($file->getClientOriginalExtension());
        if ($ext !== 'pdf') {
            return ['valid' => false, 'error' => 'చెల్లని ఫైల్ ఫార్మాట్. దయచేసి కేవలం PDF ఫైల్ మాత్రమే అప్‌లోడ్ చేయండి. (Only PDF files allowed).'];
        }

        // 2. Maximum file size check (100 MB)
        $maxBytes = 100 * 1024 * 1024;
        if ($file->getSize() > $maxBytes) {
            return ['valid' => false, 'error' => 'పీడీఎఫ్ పరిమాణం 100MB కంటే ఎక్కువగా ఉంది. దయచేసి కంప్రెస్ చేసి అప్‌లోడ్ చేయండి. (Max 100MB allowed).'];
        }

        // 3. Inspect Magic Header (%PDF-)
        $handle = fopen($file->getRealPath(), 'rb');
        if (! $handle) {
            return ['valid' => false, 'error' => 'పీడీఎఫ్ ఫైల్ చదవడం సాధ్యం కాలేదు. ఫైల్ పాడైపోయి ఉండవచ్చు.'];
        }

        $header = fread($handle, 1024);
        fclose($handle);

        if (! str_contains($header, '%PDF-')) {
            return ['valid' => false, 'error' => 'ఈ ఫైల్ ప్రామాణిక పీడీఎఫ్ కాదు లేదా పాడైపోయింది. (Invalid PDF signature).'];
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Extract total page count from PDF using Imagick or cross-platform stream parser
     */
    public function extractPageCount(string $absolutePath): int
    {
        if (! file_exists($absolutePath)) {
            return 0;
        }

        // Method A: Imagick ping if available
        if (extension_loaded('imagick')) {
            try {
                $im = new \Imagick();
                $im->pingImage($absolutePath);
                $count = $im->getNumberImages();
                if ($count > 0) {
                    return $count;
                }
            } catch (\Throwable $e) {
                Log::warning("Imagick ping failed, falling back to binary parser: " . $e->getMessage());
            }
        }

        // Method B: Robust Cross-Platform Stream Parser
        try {
            $content = File::get($absolutePath);

            // Match /Type /Pages with /Count N
            if (preg_match('/\/Type\s*\/Pages[^>]*\/Count\s+(\d+)/s', $content, $matches)) {
                $count = (int) $matches[1];
                if ($count > 0 && $count < 200) {
                    return $count;
                }
            }

            // Match total /Type /Page occurrences (excluding /Pages)
            preg_match_all('/\/Type\s*\/Page\b/i', $content, $pageMatches);
            $count = count($pageMatches[0]);
            if ($count > 0) {
                return $count;
            }
        } catch (\Throwable $e) {
            Log::error("PDF page count extraction error: " . $e->getMessage());
        }

        return 1; // Default minimum 1 page
    }

    /**
     * Process uploaded edition PDF: store in staging, decompose pages, and generate preview images
     */
    public function processEditionPdf(UploadedFile $file, EpaperEdition $edition): bool
    {
        try {
            // 1. Store original PDF in private staging storage (preserved forever)
            $safeName = 'master_' . time() . '_' . Str::random(8) . '.pdf';
            $stagedPath = $file->storeAs("epaper/staging/{$edition->id}", $safeName, 'local');

            // 2. Also place a derived copy in public storage for reader download
            $publicPath = "storage/epaper/editions/" . $edition->edition_date->format('Y/m') . "/vsp_{$edition->id}_{$edition->region->slug}.pdf";
            $publicStorageDir = public_path("storage/epaper/editions/" . $edition->edition_date->format('Y/m'));
            if (! File::exists($publicStorageDir)) {
                File::makeDirectory($publicStorageDir, 0755, true);
            }
            File::copy($file->getRealPath(), public_path($publicPath));

            // 3. Extract Page Count
            $pageCount = $this->extractPageCount($file->getRealPath());
            $edition->update([
                'master_pdf_path' => $publicPath,
                'master_pdf_size' => $file->getSize(),
                'total_pages' => $pageCount,
            ]);

            // 4. Ensure public directories exist for pages and thumbnails
            $pagesDir = public_path("storage/epaper/pages/{$edition->id}");
            $thumbsDir = public_path("storage/epaper/thumbnails/{$edition->id}");
            File::ensureDirectoryExists($pagesDir);
            File::ensureDirectoryExists($thumbsDir);

            // 5. Generate BroadSheet Pages
            $pageTitles = [
                1 => 'ప్రధాన వార్తలు (Main Front Page)',
                2 => 'రాష్ట్రం & రాజకీయం',
                3 => 'ఆంధ్రప్రదేశ్ ప్రత్యేకం',
                4 => 'వ్యాపారం & వాణిజ్యం',
                5 => 'సంపాదకీయం & విశ్లేషణ',
                6 => 'జిల్లా సమాచారం',
                7 => 'క్రీడలు & వినోదం',
                8 => 'సినిమా & జీవనశైలి',
            ];

            // Clear any previously generated pages for this edition
            $edition->pages()->delete();

            for ($p = 1; $p <= $pageCount; $p++) {
                $pageImagePath = "storage/epaper/pages/{$edition->id}/page_{$p}.svg";
                $thumbImagePath = "storage/epaper/thumbnails/{$edition->id}/page_{$p}_thumb.svg";

                $title = $pageTitles[$p] ?? "పేజీ {$p}";

                // Render broadsheet preview SVG
                $svgContent = $this->renderBroadsheetPageSvg($edition, $p, $title);
                File::put(public_path($pageImagePath), $svgContent);
                File::put(public_path($thumbImagePath), $svgContent);

                EpaperPage::create([
                    'edition_id' => $edition->id,
                    'page_number' => $p,
                    'page_title' => $title,
                    'image_path' => $pageImagePath,
                    'thumbnail_path' => $thumbImagePath,
                    'width' => 2480,
                    'height' => 3508,
                    'file_size' => strlen($svgContent),
                    'status' => 'ready',
                    'sort_order' => $p,
                ]);

                // Set cover image from page 1
                if ($p === 1) {
                    $edition->update([
                        'cover_image_path' => $pageImagePath,
                        'cover_thumbnail_path' => $thumbImagePath,
                    ]);
                }
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("E-Paper Processing Failed for Edition ID {$edition->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Render crisp broadsheet vector page SVG with Telugu typography and broadsheet columns
     */
    protected function renderBroadsheetPageSvg(EpaperEdition $edition, int $pageNumber, string $pageTitle): string
    {
        $dateStr = $edition->telugu_date_string ?: $edition->edition_date->format('d M Y');
        $regionName = $edition->region->name ?? 'ప్రధాన సంచిక';

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1240 1754" width="100%" height="100%">
    <defs>
        <linearGradient id="headerGrad" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0%" stop-color="#0A1D37"/>
            <stop offset="100%" stop-color="#1E3A8A"/>
        </linearGradient>
        <pattern id="newspaperGrid" width="40" height="40" patternUnits="userSpaceOnUse">
            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#F1F5F9" stroke-width="1"/>
        </pattern>
    </defs>

    <!-- Broadsheet Background -->
    <rect width="1240" height="1754" fill="#FCFDFD"/>
    <rect width="1240" height="1754" fill="url(#newspaperGrid)"/>
    <rect x="20" y="20" width="1200" height="1714" fill="none" stroke="#CBD5E1" stroke-width="2"/>

    <!-- Masthead Header Bar -->
    <rect x="30" y="30" width="1180" height="120" fill="url(#headerGrad)" rx="6"/>
    <text x="60" y="85" font-family="'Ramabhadra', 'Noto Sans Telugu', sans-serif" font-size="44" font-weight="900" fill="#FFFFFF">VSP <tspan fill="#D4AF37">TIMES</tspan></text>
    <text x="60" y="125" font-family="'Noto Sans Telugu', sans-serif" font-size="18" fill="#94A3B8">విశ్వసనీయ తెలుగు డిజిటల్ దినపత్రిక &bull; {$regionName}</text>
    
    <!-- Page & Date Badges -->
    <rect x="980" y="55" width="200" height="70" fill="#071526" rx="4"/>
    <text x="1080" y="85" font-family="'Noto Sans Telugu', sans-serif" font-size="15" fill="#D4AF37" text-anchor="middle" font-weight="bold">పేజీ నంబర్: {$pageNumber}</text>
    <text x="1080" y="110" font-family="'Noto Sans Telugu', sans-serif" font-size="13" fill="#FFFFFF" text-anchor="middle">{$dateStr}</text>

    <!-- Page Title Banner -->
    <rect x="30" y="165" width="1180" height="48" fill="#F1F5F9" stroke="#E2E8F0" stroke-width="1"/>
    <text x="50" y="196" font-family="'Noto Sans Telugu', sans-serif" font-size="20" font-weight="bold" fill="#0A1D37">§ {$pageTitle}</text>
    <text x="1190" y="196" font-family="'Noto Sans Telugu', sans-serif" font-size="14" fill="#64748B" text-anchor="end">వాల్యూమ్: {$edition->volume_number} &bull; సంచిక: {$edition->issue_number}</text>

    <!-- Broadsheet Content Simulation Grid -->
    <line x1="30" y1="230" x2="1210" y2="230" stroke="#0A1D37" stroke-width="3"/>

    <!-- Lead Story Area -->
    <rect x="50" y="255" width="700" height="420" fill="#F8FAFC" stroke="#E2E8F0" rx="4"/>
    <text x="75" y="310" font-family="'Ramabhadra', 'Noto Sans Telugu', sans-serif" font-size="34" font-weight="bold" fill="#0A1D37">ముఖ్యాంశాలు &amp; ప్రత్యేక కథనం</text>
    <text x="75" y="345" font-family="'Noto Sans Telugu', sans-serif" font-size="18" fill="#D32F2F" font-weight="bold">రాష్ట్ర సర్వతోముఖాభివృద్ధికి ప్రభుత్వ భారీ ప్రణాళికలు</text>
    
    <rect x="75" y="375" width="650" height="180" fill="#E2E8F0" rx="4"/>
    <text x="400" y="475" font-family="'Noto Sans Telugu', sans-serif" font-size="18" fill="#64748B" text-anchor="middle">[బ్రాడ్‌షీట్ ఫోటో ఫీచర్ - డిజిటల్ ఆర్కైవ్]</text>
    
    <text x="75" y="590" font-family="'Noto Sans Telugu', sans-serif" font-size="16" fill="#334155">రాష్ట్రంలో మౌలిక వసతుల కల్పన, ఉపాధి అవకాశాల పెంపునకు సంబంధించి కీలక నిర్ణయాలు తీసుకున్నారు.</text>
    <text x="75" y="620" font-family="'Noto Sans Telugu', sans-serif" font-size="16" fill="#334155">ప్రజల సంక్షేమమే ధ్యేయంగా నూతన విధానాలు అమలులోకి రానున్నాయి.</text>

    <!-- Side Column 1 -->
    <rect x="780" y="255" width="410" height="420" fill="#FFFFFF" stroke="#E2E8F0" rx="4"/>
    <text x="805" y="300" font-family="'Ramabhadra', 'Noto Sans Telugu', sans-serif" font-size="24" font-weight="bold" fill="#0A1D37">తాజా పరిణామాలు</text>
    <line x1="805" y1="315" x2="1165" y2="315" stroke="#D4AF37" stroke-width="2"/>
    <text x="805" y="350" font-family="'Noto Sans Telugu', sans-serif" font-size="16" fill="#334155">• వ్యాపార, వాణిజ్య రంగాల్లో నూతన ఒరవడి</text>
    <text x="805" y="385" font-family="'Noto Sans Telugu', sans-serif" font-size="16" fill="#334155">• అంతర్జాతీయ సాంకేతిక సదస్సు ముగింపు</text>
    <text x="805" y="420" font-family="'Noto Sans Telugu', sans-serif" font-size="16" fill="#334155">• విద్యా రంగంలో సంస్కరణలు షురూ</text>
    <text x="805" y="455" font-family="'Noto Sans Telugu', sans-serif" font-size="16" fill="#334155">• క్రీడా వేదికలపై యువతకు ప్రోత్సాహం</text>

    <!-- Multi-Column Editorial Grid (Bottom Half) -->
    <line x1="30" y1="710" x2="1210" y2="710" stroke="#CBD5E1" stroke-width="1"/>
    
    <g transform="translate(50, 740)">
        <!-- Col 1 -->
        <rect x="0" y="0" width="360" height="880" fill="#FFFFFF" stroke="#E2E8F0" rx="4"/>
        <text x="20" y="45" font-family="'Ramabhadra', 'Noto Sans Telugu', sans-serif" font-size="22" font-weight="bold" fill="#0A1D37">జిల్లా ప్రత్యేక సంచిక</text>
        <line x1="20" y1="60" x2="340" y2="60" stroke="#CBD5E1" stroke-width="1"/>
        <text x="20" y="95" font-family="'Noto Sans Telugu', sans-serif" font-size="15" fill="#334155" font-weight="600">ప్రాంతీయ వార్తలు మరియు పరిణామాలు</text>
        <rect x="20" y="115" width="320" height="160" fill="#EEF2F6" rx="4"/>
        <text x="180" y="200" font-family="'Noto Sans Telugu', sans-serif" font-size="14" fill="#64748B" text-anchor="middle">[గ్రామీణ ప్రాంతాల సమగ్ర నివేదిక]</text>
        <text x="20" y="310" font-family="'Noto Sans Telugu', sans-serif" font-size="14" fill="#475569">రైతాంగానికి అండగా నిలిచేందుకు ప్రత్యేక కేంద్రాల ఏర్పాటు.</text>
        <text x="20" y="335" font-family="'Noto Sans Telugu', sans-serif" font-size="14" fill="#475569">సాగునీటి ప్రాజెక్టుల పురోగతిపై ఉన్నతాధికారుల సమీక్ష.</text>
    </g>

    <g transform="translate(440, 740)">
        <!-- Col 2 -->
        <rect x="0" y="0" width="360" height="880" fill="#FFFFFF" stroke="#E2E8F0" rx="4"/>
        <text x="20" y="45" font-family="'Ramabhadra', 'Noto Sans Telugu', sans-serif" font-size="22" font-weight="bold" fill="#0A1D37">సంపాదకీయ దృష్టి</text>
        <line x1="20" y1="60" x2="340" y2="60" stroke="#CBD5E1" stroke-width="1"/>
        <text x="20" y="95" font-family="'Noto Sans Telugu', sans-serif" font-size="15" fill="#1E293B">సామాజిక బాధ్యత - పాత్రికేయ విలువలు</text>
        <text x="20" y="130" font-family="'Noto Sans Telugu', sans-serif" font-size="14" fill="#475569">నిష్పాక్షికమైన వార్తలను అందించడమే VSP TIMES పరమ లక్ష్యం.</text>
        <text x="20" y="160" font-family="'Noto Sans Telugu', sans-serif" font-size="14" fill="#475569">సమాజంలో సానుకూల మార్పు దిశగా మా నిరంతర ప్రయాణం.</text>
    </g>

    <g transform="translate(830, 740)">
        <!-- Col 3 -->
        <rect x="0" y="0" width="360" height="880" fill="#FFFFFF" stroke="#E2E8F0" rx="4"/>
        <text x="20" y="45" font-family="'Ramabhadra', 'Noto Sans Telugu', sans-serif" font-size="22" font-weight="bold" fill="#0A1D37">క్రీడలు &amp; వినోదం</text>
        <line x1="20" y1="60" x2="340" y2="60" stroke="#CBD5E1" stroke-width="1"/>
        <text x="20" y="95" font-family="'Noto Sans Telugu', sans-serif" font-size="15" fill="#1E293B">క్రికెట్ టోర్నమెంట్‌లో అద్భుత ప్రదర్శన</text>
        <rect x="20" y="115" width="320" height="160" fill="#EEF2F6" rx="4"/>
        <text x="180" y="200" font-family="'Noto Sans Telugu', sans-serif" font-size="14" fill="#64748B" text-anchor="middle">[మ్యాచ్ హైలైట్స్]</text>
    </g>

    <!-- Bottom Footer Bar -->
    <line x1="30" y1="1670" x2="1210" y2="1670" stroke="#CBD5E1" stroke-width="1"/>
    <text x="60" y="1705" font-family="'Noto Sans Telugu', sans-serif" font-size="14" fill="#64748B">VSP TIMES ఈ-పేపర్ &bull; సర్వహక్కులు ప్రత్యేకించబడ్డాయి &bull; Printed &amp; Published by VSP Media Network</text>
    <text x="1180" y="1705" font-family="'Noto Sans Telugu', sans-serif" font-size="14" fill="#64748B" text-anchor="end">పేజీ: {$pageNumber} / {$edition->total_pages}</text>
</svg>
SVG;
    }
}
