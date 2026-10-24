<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    protected string $settingsFile;

    public function __construct()
    {
        $this->settingsFile = storage_path('app/epaper_settings.json');
    }

    public function index()
    {
        $settings = $this->getSettings();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'timezone' => ['required', 'string'],
            'default_publish_time' => ['required', 'string'],
            'max_pdf_size_mb' => ['required', 'integer', 'min:5', 'max:500'],
            'auto_archive_days' => ['required', 'integer', 'min:30', 'max:3650'],
            'allow_public_downloads' => ['nullable', 'boolean'],
            'contact_email' => ['required', 'email'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
        ]);

        $validated['allow_public_downloads'] = $request->has('allow_public_downloads');

        File::put($this->settingsFile, json_encode($validated, JSON_PRETTY_PRINT));

        return back()->with('success', 'ఈ-పేపర్ సిస్టమ్ సెట్టింగ్స్ విజయవంతంగా సేవ్ చేయబడ్డాయి.');
    }

    protected function getSettings(): array
    {
        if (File::exists($this->settingsFile)) {
            return json_decode(File::get($this->settingsFile), true) ?: $this->defaultSettings();
        }

        return $this->defaultSettings();
    }

    protected function defaultSettings(): array
    {
        return [
            'site_name' => 'VSP TIMES E-Paper',
            'timezone' => 'Asia/Kolkata',
            'default_publish_time' => '05:00',
            'max_pdf_size_mb' => 100,
            'auto_archive_days' => 365,
            'allow_public_downloads' => true,
            'contact_email' => 'epaper@vspmedianetwork.com',
            'whatsapp_number' => '+91 98480 12345',
        ];
    }
}
