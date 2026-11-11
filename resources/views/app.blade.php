<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @isset($og)
    <title>{{ $og['title'] }}</title>
    <meta name="description" content="{{ $og['description'] }}" />
    <meta property="og:title" content="{{ $og['title'] }}" />
    <meta property="og:description" content="{{ $og['description'] }}" />
    <meta property="og:url" content="{{ $og['url'] }}" />
    <meta property="og:type" content="article" />
    @if(!empty($og['image']))
    <meta property="og:image" content="{{ $og['image'] }}" />
    @endif
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $og['title'] }}" />
    <meta name="twitter:description" content="{{ $og['description'] }}" />
    @if(!empty($og['image']))
    <meta name="twitter:image" content="{{ $og['image'] }}" />
    @endif
    @endisset
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="bg-gray-100">
    @inertia
</body>
</html>
