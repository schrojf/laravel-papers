<?php

use Schrojf\Papers\Papers;
use Tests\fixtures\Papers\WithExceptionTestPaper;

test('that exception was caught', function () {
    $paper = new WithExceptionTestPaper;

    $content = $paper->resolveContent();

    expect($content)->toBeArray();
});

test('paper contains exception name and message', function () {
    Papers::register([
        WithExceptionTestPaper::class,
    ]);

    $response = $this->get('/papers/with-exception-test-paper');

    $response->assertOk();

    $response->assertSeeTextInOrder([
        'With Exception Test Paper',
        'Test paper which will raise exception in one of its cells.',

        'First section',
        'OK',

        'Second section',
        'RuntimeException',
        'Test paper which will raise an exception.',

        '1 cell could not be run due to exception in previous cell:',
        'Third section',
    ]);

    $response->assertDontSeeText([
        'Three',
    ]);
});
