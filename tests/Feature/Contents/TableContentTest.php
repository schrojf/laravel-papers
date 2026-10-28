<?php

use Schrojf\Papers\Contents\TableContent;

test('row can not have more columns that headers', function () {
    $content = TableContent::withHeaders(['header A', 'header B']);

    expect(fn () => $content->row(['1', '2', '3']))
        ->toThrow(InvalidArgumentException::class, 'Row has more cells than headers.');
});

test('data panel content is rendered', function () {
    $content = TableContent::withHeaders(['header A', 'header B'])
        ->row(['cell A1', 'cell A2'])
        ->rows([
            [null, 'cell B2'],
            ['cell C1'],
        ])
        ->render();

    expect($content)->toContain('header A', 'header B')
        ->toContain('cell A1', 'cell A2')
        ->toContain('cell B2')
        ->toContain('cell C1');
});

test('table content keeps cells on one line and scrolls by default', function () {
    $content = TableContent::withHeaders(['header A'])
        ->row(['cell A1'])
        ->render();

    expect($content)->toContain('min-w-max', 'whitespace-nowrap')
        ->not->toContain('min-w-full', '[overflow-wrap:anywhere]');
});

test('table content can be fitted to the section width', function () {
    $content = TableContent::withHeaders(['header A'])
        ->row(['cell A1'])
        ->fit()
        ->render();

    expect($content)->toContain('min-w-full', '[overflow-wrap:anywhere]')
        ->not->toContain('min-w-max', 'whitespace-nowrap');
});

test('table content fit can be turned off', function () {
    $content = TableContent::withHeaders(['header A'])
        ->row(['cell A1'])
        ->fit()
        ->fit(false);

    expect($content->toArray()['fit'])->toBeFalse()
        ->and($content->render())->toContain('min-w-max', 'whitespace-nowrap');
});
