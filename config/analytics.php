<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Tag Manager (GTM) Configuration
    |--------------------------------------------------------------------------
    | Container ID format: GTM-XXXXXXX
    | Left empty until configured in .env. Integrations remain inactive.
    */
    'gtm_container_id' => env('GTM_CONTAINER_ID', null),

    /*
    |--------------------------------------------------------------------------
    | Google Analytics 4 (GA4) Configuration
    |--------------------------------------------------------------------------
    | Measurement ID format: G-XXXXXXXXXX
    */
    'ga4_measurement_id' => env('EPAPER_GA4_MEASUREMENT_ID', env('GA4_MEASUREMENT_ID', 'G-VHYRSKXX6D')),

    /*
    |--------------------------------------------------------------------------
    | Google Ads Conversion Tracking
    |--------------------------------------------------------------------------
    | Conversion ID format: AW-XXXXXXXXX
    */
    'google_ads_conversion_id' => env('GOOGLE_ADS_CONVERSION_ID', null),
    'google_ads_conversion_label_pdf_download' => env('GOOGLE_ADS_CONVERSION_LABEL_PDF_DOWNLOAD', null),
    'google_ads_conversion_label_edition_read' => env('GOOGLE_ADS_CONVERSION_LABEL_EDITION_READ', null),

    /*
    |--------------------------------------------------------------------------
    | Cross-Domain Linking Configuration
    |--------------------------------------------------------------------------
    | Link main news portal with E-Paper subdomain without session breaks.
    */
    'cross_domains' => [
        'vspmedianetwork.com',
        'epaper.vspmedianetwork.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Readership Cache Settings
    |--------------------------------------------------------------------------
    */
    'cache_ttl_seconds' => env('ANALYTICS_CACHE_TTL_SECONDS', 900), // 15 minutes
];
