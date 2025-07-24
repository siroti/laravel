<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="description" content="@yield('description')">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://s3.amazonaws.com">
    <link rel="dns-prefetch" href="https://s3.amazonaws.com">
    <link rel="preconnect" href="https://media.sub100.com.br">
    <link rel="dns-prefetch" href="https://media.sub100.com.br">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

     @hasSection('rob')
        @yield('rob')
    @else
        <meta name="robots" content="index, follow">
    @endif
    @stack('meta')
    <link rel="canonical" href="{{ url()->current() }}">
    
    <link rel="shortcut icon" href="{{ $config['images']['image_favicon'] ? env('PANEL_UPLOADPATH_S3').$config['images']['image_favicon'] : '/images/favicon.ico' }}">
    @include('base.partials.colors')
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/scss/custom.scss'])
    @stack('css-push')
</head>
<body>
   
    <div id="loading" class="d-none position-absolute bg-body bg-opacity-75 justify-content-center align-items-center w-100 h-100 z-7">
        <div class="spinner-border text-secondary" style="width: 5rem; height: 5rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <style>@keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}</style>
    
    @include('header.header1') 
    @yield('content')
    @include('footer.footer1')
    @stack('js-push')
    @stack('endjs-push')
</body>
</html>
