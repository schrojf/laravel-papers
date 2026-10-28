<?php

namespace Tests\Feature;

use Illuminate\Support\HtmlString;
use Schrojf\Papers\Papers;
use Tests\fixtures\Papers\EmptyTestPaper;
use Tests\fixtures\Papers\FullWidthTestPaper;
use Tests\fixtures\Papers\SearchableTestPaper;
use Tests\fixtures\Papers\SimpleTestPaper;

it('has name derived from its class name', function () {
    expect(SimpleTestPaper::name())->toBe('Simple Test Paper');
});

it('has url handler derived from its class name', function () {
    expect(SimpleTestPaper::handler())->toBe('simple-test-paper');
});

it('has no default description set', function () {
    expect(EmptyTestPaper::$description)->toBeNull()
        ->and(EmptyTestPaper::description())->toBeNull();
});

it('has description', function () {
    expect(SimpleTestPaper::$description)->toBe('Simple test paper description.');

    SimpleTestPaper::$description = 'This is a test paper';
    expect(SimpleTestPaper::description())->toBe('This is a test paper');

    // after
    SimpleTestPaper::$description = 'Simple test paper description.';
});

it('is not full width by default', function () {
    expect(SimpleTestPaper::$fullWidth)->toBeFalse()
        ->and(SimpleTestPaper::fullWidth())->toBeFalse();
});

it('can be full width', function () {
    expect(FullWidthTestPaper::fullWidth())->toBeTrue();
});

it('has no keywords by default', function () {
    expect(SimpleTestPaper::$keywords)->toBe([])
        ->and(SimpleTestPaper::keywords())->toBe([]);
});

it('has plain text search data', function () {
    expect(SimpleTestPaper::searchData())->toBe([
        'name' => 'Simple Test Paper',
        'description' => 'Simple test paper description.',
        'handler' => 'simple-test-paper',
        'keywords' => [],
    ]);

    expect(SearchableTestPaper::searchData())->toBe([
        'name' => 'Predaj "podľa" značiek & kolekcií',
        'description' => 'Sales by brand',
        'handler' => 'searchable-test-paper',
        'keywords' => ['tržby', 'revenue'],
    ]);
});

it('has no papers registered', function () {
    expect(Papers::all())->toBeEmpty();
});

it('can swap entire paper collection', function () {
    Papers::register([
        SimpleTestPaper::class,
    ]);

    Papers::replacePapers([
        'Form Validation',
        'Data Processing',
    ]);

    expect(Papers::all())->toBe([
        'Form Validation',
        'Data Processing',
    ]);
});

it('can register a paper', function () {
    Papers::register([
        SimpleTestPaper::class,
    ]);

    expect(Papers::all())->toBe([SimpleTestPaper::class]);
});

it('will register same paper only once', function () {
    Papers::register([
        SimpleTestPaper::class,
        SimpleTestPaper::class,
        SimpleTestPaper::class,
    ]);

    expect(Papers::all())->toBe([SimpleTestPaper::class]);
});

it('can resolve a paper by its handler', function () {
    Papers::register([
        SimpleTestPaper::class,
    ]);

    expect(Papers::paperForHandler('simple-test-paper'))->toBe(SimpleTestPaper::class)
        ->and(Papers::paperForHandler('not-registered'))->toBeNull();
});

it('renders nothing into the head by default', function () {
    expect(Papers::renderHead()->toHtml())->toBe('');
});

it('renders the favicon with an escaped URL', function () {
    Papers::favicon('/favicon.png?v=1&size=32');

    expect(Papers::renderHead()->toHtml())->toBe('<link rel="icon" href="/favicon.png?v=1&amp;size=32">'.PHP_EOL);
});

it('renders head HTML as it is', function () {
    Papers::head('<meta name="theme-color" content="#16a34a">');

    expect(Papers::renderHead()->toHtml())->toBe('<meta name="theme-color" content="#16a34a">');

    Papers::head(new HtmlString('<link rel="manifest" href="/papers.webmanifest">'));

    expect(Papers::renderHead()->toHtml())->toBe('<link rel="manifest" href="/papers.webmanifest">');
});

it('renders head HTML from a closure whenever the head is rendered', function () {
    $calls = 0;

    Papers::head(function () use (&$calls) {
        $calls++;

        return new HtmlString('<link rel="apple-touch-icon" href="/apple-touch-icon.png">');
    });

    expect($calls)->toBe(0);

    Papers::favicon('/favicon.png');

    expect(Papers::renderHead()->toHtml())->toBe('<link rel="icon" href="/favicon.png">'.PHP_EOL.'<link rel="apple-touch-icon" href="/apple-touch-icon.png">')
        ->and(Papers::renderHead()->toHtml())->toContain('apple-touch-icon')
        ->and($calls)->toBe(2);
});
