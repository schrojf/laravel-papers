<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'ddd', 'dump', 'env', 'ray', 'md5', 'md5_file'])
    ->each->not->toBeUsed();

arch('disallowed classes and functions')
    ->expect([
        // We should always reference \Symfony\HttpFoundation\Response as not all
        // responses are converted or wrapped in Laravel's response class, e.g.,
        // downloads via `response()->download()` return a Symfony response class.
        \Illuminate\Http\Response::class,
        // We should be catching \Throwable.
        // We should be throwing `RuntimeException`.
        // This rule can be removed if it doesn't work out well in practice.
        \Exception::class,
    ])
    ->each->not->toBeUsed();
