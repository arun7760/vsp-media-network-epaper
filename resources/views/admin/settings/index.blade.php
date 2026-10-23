@extends('layouts.admin')

@section('title', 'సిస్టమ్ సెట్టింగ్స్ - VSP TIMES E-Paper')
@section('header_title', 'ఈ-పేపర్ ప్రచురణ & సిస్టమ్ సెట్టింగ్స్ (Settings)')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <h2 style="font-family: 'Ramabhadra', sans-serif; font-size: 22px; color: var(--admin-navy);">
            <i class="fa-solid fa-sliders" style="color: var(--admin-gold); margin-right: 8px;"></i>
            ప్రచురణ &amp; సాధారణ ఆకృతీకరణ
        </h2>
        <p style="color: var(--admin-muted); font-size: 13px; margin-top: 4px;">
            ఆటోమేటిక్ ప్రచురణ సమయం, గరిష్ట పీడీఎఫ్ పరిమాణం, ఆర్కైవ్ నిల్వ మరియు సంప్రదింపు వివరాలు.
        </p>
    </div>

    <div class="card">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            <h3 style="font-family: 'Ramabhadra', sans-serif; font-size: 16px; color: var(--admin-navy); margin-bottom: 16px; border-bottom: 1px solid var(--admin-border); padding-bottom: 8px;">
                <i class="fa-solid fa-newspaper" style="color: var(--admin-gold); margin-right: 6px;"></i>
                ప్రచురణ నియమాలు (Publishing Rules)
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                        డిఫాల్ట్ ప్రచురణ సమయం (Default Scheduled Time) *
                    </label>
                    <input type="time" name="default_publish_time" value="{{ old('default_publish_time', $settings['default_publish_time'] ?? '05:00') }}" required
                           style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px;">
                    <span style="font-size: 11px; color: var(--admin-muted);">సాధారణంగా ప్రతిరోజూ ఉదయం 05:00 AM IST</span>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                        టైమ్‌జోన్ (System Timezone) *
                    </label>
                    <input type="text" name="timezone" value="{{ old('timezone', $settings['timezone'] ?? 'Asia/Kolkata') }}" readonly
                           style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px; background: #F1F5F9; color: #64748B;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                        గరిష్ట పీడీఎఫ్ పరిమాణం (Max PDF Size in MB) *
                    </label>
                    <input type="number" name="max_pdf_size_mb" value="{{ old('max_pdf_size_mb', $settings['max_pdf_size_mb'] ?? 100) }}" min="10" max="500" required
                           style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                        ఆర్కైవ్ నిల్వ వ్యవధి (రోజులలో) *
                    </label>
                    <input type="number" name="auto_archive_days" value="{{ old('auto_archive_days', $settings['auto_archive_days'] ?? 365) }}" min="30" max="3650" required
                           style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px;">
                </div>
            </div>

            <div style="margin-bottom: 24px; padding: 14px; background: #F8FAFC; border: 1px solid var(--admin-border); border-radius: 8px;">
                <label style="display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 14px; color: var(--admin-navy); cursor: pointer;">
                    <input type="checkbox" name="allow_public_downloads" value="1" {{ !empty($settings['allow_public_downloads']) ? 'checked' : '' }}
                           style="width: 18px; height: 18px; accent-color: var(--admin-gold);">
                    <span>ప్రజలకు ఉచిత పీడీఎఫ్ డౌన్‌లోడ్ అనుమతించండి (Allow Free PDF Downloads)</span>
                </label>
                <p style="font-size: 12px; color: var(--admin-muted); margin-top: 4px; margin-left: 28px;">
                    ఇది ఎనేబుల్ చేయబడితే రీడర్‌లో "డౌన్‌లోడ్ పీడీఎఫ్" బటన్ కనిపిస్తుంది.
                </p>
            </div>

            <h3 style="font-family: 'Ramabhadra', sans-serif; font-size: 16px; color: var(--admin-navy); margin-bottom: 16px; border-bottom: 1px solid var(--admin-border); padding-bottom: 8px;">
                <i class="fa-solid fa-address-book" style="color: var(--admin-gold); margin-right: 6px;"></i>
                పోర్టల్ సమాచారం (Portal Information)
            </h3>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                    పోర్టల్ పేరు (Site Name) *
                </label>
                <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'VSP TIMES E-Paper') }}" required
                       style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                        సంప్రదింపు ఈమెయిల్ (Contact Email) *
                    </label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? 'epaper@vspmedianetwork.com') }}" required
                           style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                        వాట్సాప్ సహాయక సంఖ్య (WhatsApp Support)
                    </label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '+91 98480 12345') }}"
                           style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 14px; border-top: 1px solid var(--admin-border); padding-top: 20px;">
                <button type="submit" class="btn-admin btn-gold" style="padding: 11px 24px;">
                    <i class="fa-solid fa-floppy-disk"></i> సెట్టింగ్స్ భద్రపరచండి
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
