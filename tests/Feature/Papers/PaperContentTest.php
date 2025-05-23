<?php

use Schrojf\Papers\Papers;
use Tests\fixtures\Papers\EmptyTestPaper;
use Tests\fixtures\Papers\SimpleTestPaper;

test('paper has defined sections', function () {
    $paper = new SimpleTestPaper;

    $sections = $paper->sections();

    expect($sections)->toHaveKeys([
        'First section',
        'Second section',
        'Third section',
    ]);
});

test('sections are resolved with content', function () {
    $paper = new SimpleTestPaper;

    $content = $paper->resolveContent();

    expect($content)->toBe([
        ['type' => 'section', 'name' => 'First section', 'content' => ['One']],
        ['type' => 'section', 'name' => 'Second section', 'content' => []],
        ['type' => 'section', 'name' => 'Third section', 'content' => ['Two', 'Three']],
    ]);
});

test('message is shown when paper has no defined sections', function () {
    Papers::register([
        SimpleTestPaper::class,
        EmptyTestPaper::class,
    ]);

    $response = $this->get('/papers/empty-test-paper');

    $response->assertOk();

    $response->assertSeeTextInOrder([
        EmptyTestPaper::name(),
        'Nothing to show here… yet.',
        'This area is currently empty.',
    ]);
})->after(function () {
    Papers::replacePapers([]);
});

test('content from defined sections is show on paper page', function () {
    Papers::register([
        SimpleTestPaper::class,
    ]);

    $response = $this->get('/papers/simple-test-paper');

    $response->assertOk();

    $response->assertSeeTextInOrder([
        SimpleTestPaper::name(),
        SimpleTestPaper::description(),
        'First section',
        'One',
        'Second section',
        'Empty cell',
        'Third section',
        'Two',
        'Three',
    ]);
})->after(function () {
    Papers::replacePapers([]);
});
