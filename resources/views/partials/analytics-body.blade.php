@php
    $siteSettings = \App\Support\SchemaCheck::has('platform_settings')
        ? \App\Models\PlatformSetting::current()
        : null;
@endphp

@if($siteSettings)
    @if($siteSettings->gtm_container_id)
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $siteSettings->gtm_container_id }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    @if($siteSettings->custom_body_html)
        {!! $siteSettings->custom_body_html !!}
    @endif
@endif
