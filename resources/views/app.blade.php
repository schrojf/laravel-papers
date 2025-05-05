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

    <title>Laravel Papers{{ config('app.name') ? ' - ' . config('app.name') : '' }}</title>

    <!-- Style sheets -->
    {!! Papers::css() !!}

    <!-- JavaScript scripts -->
    {!! Papers::js() !!}
</head>
<body class="bg-gray-100 dark:bg-gray-900 font-sans antialiased">
    <div class="min-h-screen">
        <main class="px-3 lg:px-5">
            <h1 class="text-3xl font-bold underline">
                Hello world!
            </h1>
        </main>
    </div>
</body>
</html>
