@php
    $siteSettings = \App\Support\SchemaCheck::has('platform_settings')
        ? \App\Models\PlatformSetting::current()
        : null;
@endphp

@if($siteSettings)
    {{-- Site verification meta tags (Google Search Console + Bing) --}}
    @if($siteSettings->google_site_verification)
        <meta name="google-site-verification" content="{{ $siteSettings->google_site_verification }}">
    @endif
    @if($siteSettings->bing_site_verification)
        <meta name="msvalidate.01" content="{{ $siteSettings->bing_site_verification }}">
    @endif

    {{-- Google Tag Manager (loads once, everything else can be fired through GTM) --}}
    @if($siteSettings->gtm_container_id)
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});
            var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
            j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','{{ $siteSettings->gtm_container_id }}');
        </script>
    @endif

    {{-- GA4 direct (skip if GTM already loads it) --}}
    @if($siteSettings->ga4_measurement_id && ! $siteSettings->gtm_container_id)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $siteSettings->ga4_measurement_id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $siteSettings->ga4_measurement_id }}', { anonymize_ip: true });
        </script>
    @endif

    {{-- Meta Pixel (Facebook / Instagram ads attribution) --}}
    @if($siteSettings->meta_pixel_id)
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
                n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
                document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $siteSettings->meta_pixel_id }}');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $siteSettings->meta_pixel_id }}&ev=PageView&noscript=1"/></noscript>
    @endif

    {{-- LinkedIn Insight Tag --}}
    @if($siteSettings->linkedin_partner_id)
        <script>_linkedin_partner_id = "{{ $siteSettings->linkedin_partner_id }}"; window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || []; window._linkedin_data_partner_ids.push(_linkedin_partner_id);</script>
        <script>(function(l){if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])}; window.lintrk.q=[]} var s=document.getElementsByTagName("script")[0]; var b=document.createElement("script"); b.type="text/javascript";b.async=true; b.src="https://snap.licdn.com/li.lms-analytics/insight.min.js"; s.parentNode.insertBefore(b,s);})(window.lintrk);</script>
    @endif

    {{-- Hotjar (session recording + heatmaps) --}}
    @if($siteSettings->hotjar_id)
        <script>
            (function(h,o,t,j,a,r){h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:{{ $siteSettings->hotjar_id }},hjsv:6};a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;a.appendChild(r);
            })(window,document,'https://static.hotjar.com/c/hotjar-',".js?sv=");
        </script>
    @endif

    {{-- Escape hatch — paste any tag (Ahrefs, Clarity, Segment, etc.) --}}
    @if($siteSettings->custom_head_html)
        {!! $siteSettings->custom_head_html !!}
    @endif
@endif
