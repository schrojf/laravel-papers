<!doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Hello world!</title>
        @if(file_exists(asset('vendor/papers/img/papers-32.png')))
            <link rel="shortcut icon" href="{{ asset('vendor/papers/img/papers-32.png') }}">
        @endif
        <link href="{{ asset('vendor/papers/papers.css') }}" rel="stylesheet" onerror="alert('papers.css failed to load. Please refresh the page, re-publish Laravel Papers assets, or fix routing for vendor assets.')">
    </head>
    <body>
        <h1 class="text-3xl font-bold underline">
            Hello world!
        </h1>
        <script src="{{ asset('vendor/papers/papers.js') }}" onerror="alert('papers.js failed to load. Please refresh the page, re-publish Laravel Papers assets, or fix routing for vendor assets.')"></script>
    </body>
</html>
