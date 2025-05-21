@php
    use \Schrojf\Papers\Facades\Papers;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Papers'){{ config('app.name') ? ' - ' . config('app.name') : '' }}</title>

    <!-- Style sheets -->
    {!! Papers::css() !!}

    <!-- JavaScript modules -->
    {!! Papers::js() !!}
</head>
<body class="font-sans antialiased text-gray-900">
    @include('papers::partials._header')
    @includeUnless(empty($papers), 'papers::partials._nav')
    <main @unless(empty($papers)) class="lg:ml-80" @endunless>
        @yield('content')
    </main>
</body>
</html>
