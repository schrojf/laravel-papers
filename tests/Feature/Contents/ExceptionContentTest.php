<?php

use Schrojf\Papers\Contents\ExceptionContent;

it('is renderable', function () {
    $content = new ExceptionContent(new RuntimeException('Exception message.'));

    expect($content->render())->toBe($content->toHtml());
});

test('exception content', function () {
    $content = (new ExceptionContent(new RuntimeException('Exception message.')))->toHtml();

    expect($content)->toContain('RuntimeException')
        ->toContain('Exception message.');
});
