<?php

namespace Tests\Feature;

use Schrojf\Papers\Papers;
use Tests\fixtures\Papers\EmptyTestPaper;
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
})->after(function (): void {
    SimpleTestPaper::$description = 'Simple test paper description.';
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
})->after(function (): void {
    Papers::replacePapers([]);
});

it('can register a paper', function () {
    Papers::register([
        SimpleTestPaper::class,
    ]);

    expect(Papers::all())->toBe([SimpleTestPaper::class]);
})->after(function (): void {
    Papers::replacePapers([]);
});

it('will register same paper only once', function () {
    Papers::register([
        SimpleTestPaper::class,
        SimpleTestPaper::class,
        SimpleTestPaper::class,
    ]);

    expect(Papers::all())->toBe([SimpleTestPaper::class]);
})->after(function (): void {
    Papers::replacePapers([]);
});

it('can resolve a paper by its handler', function () {
    Papers::register([
        SimpleTestPaper::class,
    ]);

    expect(Papers::paperForHandler('simple-test-paper'))->toBe(SimpleTestPaper::class)
        ->and(Papers::paperForHandler('not-registered'))->toBeNull();
})->after(function (): void {
    Papers::replacePapers([]);
});
