@php
    $gtmId = config('analytics.gtm_container_id');
    $ga4Id = config('analytics.ga4_measurement_id');
    $googleAdsId = config('analytics.google_ads_conversion_id');
    $crossDomains = config('analytics.cross_domains', ['vspmedianetwork.com', 'epaper.vspmedianetwork.com']);
@endphp

<!-- Global dataLayer Initialization -->
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){ dataLayer.push(arguments); }
    gtag('js', new Date());
</script>

@if(!empty($gtmId))
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
<!-- End Google Tag Manager -->
@endif

@if(!empty($ga4Id))
<!-- Google Analytics 4 (GA4) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
<script>
    gtag('config', '{{ $ga4Id }}', {
        'cookie_domain': 'auto',
        'linker': {
            'domains': @json($crossDomains)
        },
        'send_page_view': true
    });
</script>
@endif

@if(!empty($googleAdsId))
<!-- Google Ads Conversion Tracking -->
<script>
    gtag('config', '{{ $googleAdsId }}');
</script>
@endif

<!-- VSP Unified Client-Side Event Tracker (GTM + GA4 + First-Party Verification) -->
<script>
window.vspAnalytics = {
    csrfToken: '{{ csrf_token() }}',
    trackApiUrl: '{{ url('/api/track/event') }}',
    sentEvents: new Set(),

    track: function(eventName, params = {}) {
        // Prevent duplicate client-side events within the session pageview
        const eventKey = eventName + '_' + (params.edition_id || '') + '_' + (params.page_number || '');
        if (this.sentEvents.has(eventKey)) {
            return;
        }
        this.sentEvents.add(eventKey);

        // 1. GTM dataLayer push
        window.dataLayer.push(Object.assign({
            'event': eventName,
            'timestamp': new Date().toISOString()
        }, params));

        // 2. Direct GA4 gtag event (if configured)
        if (typeof window.gtag === 'function') {
            window.gtag('event', eventName, params);
        }

        // 3. First-party server verification call
        try {
            fetch(this.trackApiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    event_type: eventName,
                    edition_id: params.edition_id || null,
                    page_number: params.page_number || null
                })
            }).catch(function(e) { /* Non-blocking fail-safe */ });
        } catch(e) {}
    }
};
</script>
