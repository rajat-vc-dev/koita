<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'AI for Impact Awards')</title>
<meta name="description" content="@yield('description', 'AI for Impact Awards')">
<meta name="keywords" content="AI awards India, Nonprofit AI awards, Social impact awards, AI for social good, Nonprofit awards India">
<meta property="og:description" content="Recognising non-profits and social purpose organisations across India that use AI to tackle real-world challenges and create measurable, inclusive impact.">
<meta property="og:image" content="https://aiawards.indialeadersforsocialsector.com/assets/koita/og-image.webp">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" sizes="48x48" href="{{ asset('assets/koita/favicon.png') }}">
<link rel="canonical" href="https://aiawards.indialeadersforsocialsector.com/">
<!-- Open Graph / Facebook / LinkedIn Meta Tags -->
<meta property="og:type" content="website" />
<meta property="og:url" content="https://aiawards.indialeadersforsocialsector.com/" />
<meta property="og:title" content="The ILSS-KCADT AI Awards 2026 | AI for Social Impact" />
<meta property="og:description" content="The ILSS–KCADT AI Awards celebrate nonprofits using AI for meaningful social impact. Discover eligibility, prizes, evaluation criteria and key dates." />
<meta property="og:image" content="https://aiawards.indialeadersforsocialsector.com/assets/koita/images/award-ceremony.jpg" />

<!-- X (Twitter) Meta Tags -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="@ilss_official" />
<meta name="twitter:url" content="https://aiawards.indialeadersforsocialsector.com/" />
<meta name="twitter:title" content="The ILSS-KCADT AI Awards 2026 | AI for Social Impact" />
<meta name="twitter:description" content="The ILSS–KCADT AI Awards celebrate nonprofits using AI for meaningful social impact. Discover eligibility, prizes, evaluation criteria and key dates." />
<meta name="twitter:image" content="https://aiawards.indialeadersforsocialsector.com/assets/koita/images/award-ceremony.jpg" />
@stack('styles')
@stack('schema')	
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TPK75JT');</script>
<!-- End Google Tag Manager -->
</head>
<body @yield('body_attrs')>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TPK75JT"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@yield('nav')
@yield('content')
@include('partials.footer')
@stack('modals')
@stack('scripts')
</body>
</html>
