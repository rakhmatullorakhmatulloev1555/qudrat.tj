<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Primary SEO -->
    <meta name="description" content="QUDRAT — умный жилой комплекс в Душанбе. Квартиры от застройщика, инвестиции от 12%, горнодобывающий бизнес. Безопасность и комфорт 24/7.">
    <meta name="keywords" content="квартиры Душанбе, жилой комплекс Таджикистан, QUDRAT, купить квартиру, инвестиции недвижимость, уголь Таджикистан">
    <meta name="author" content="QUDRAT LLC">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- hreflang для мультиязычного SEO -->
    <link rel="alternate" hreflang="ru" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="tg" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="en" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="QUDRAT — Жилой комплекс & Горнодобыча">
    <meta property="og:description" content="Умный жилой комплекс в Душанбе. Квартиры от застройщика, инвестиции от 12%, горнодобывающий бизнес.">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:locale" content="{{ app()->getLocale() }}_{{ strtoupper(app()->getLocale()) }}">
    <meta property="og:site_name" content="QUDRAT">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="QUDRAT — Жилой комплекс & Горнодобыча">
    <meta name="twitter:description" content="Умный жилой комплекс в Душанбе. Квартиры от застройщика, инвестиции от 12%.">
    <meta name="twitter:image" content="{{ asset('images/og-image.jpg') }}">

    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Schema.org Organization -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "QUDRAT LLC",
        "url": "{{ config('app.url') }}",
        "logo": "{{ asset('images/logo1.png') }}",
        "description": "Умный жилой комплекс и горнодобывающая компания в Таджикистане",
        "address": {
            "@@type": "PostalAddress",
            "streetAddress": "ул. Туракул Зехни 12-14",
            "addressLocality": "Душанбе",
            "addressCountry": "TJ"
        },
        "contactPoint": {
            "@@type": "ContactPoint",
            "telephone": "+992-00-000-00-00",
            "contactType": "customer service",
            "availableLanguage": ["Russian", "Tajik", "English"]
        },
        "sameAs": [
            "https://t.me/qudrat_tj",
            "https://wa.me/992000000000"
        ]
    }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600&display=swap" rel="stylesheet">

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="bg-[#070B16] text-white font-inter antialiased">
    @inertia
</body>
</html>
