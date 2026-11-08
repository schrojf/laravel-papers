<?php

use Illuminate\Support\HtmlString;
use Schrojf\Papers\Contents\AlertContent;

test('alert content is rendered with title and message', function () {
    $content = AlertContent::success('The order <b>#42</b> was sent.')
        ->title('Done')
        ->toHtml();

    expect($content)
        ->toContain('role="status"', 'bg-green-50')
        ->toContain('Done', 'The order &lt;b&gt;#42&lt;/b&gt; was sent.');
});

test('alert content has types', function (string $type, string $role, string $class) {
    $content = AlertContent::{$type}(new HtmlString('<em>Message</em>'))->toHtml();

    expect($content)->toContain("role=\"{$role}\"", $class, '<em>Message</em>');
})->with([
    ['success', 'status', 'bg-green-50'],
    ['error', 'alert', 'bg-red-50'],
    ['warning', 'status', 'bg-amber-50'],
    ['info', 'status', 'bg-blue-50'],
]);

test('alert content does not accept unknown types', function () {
    expect(fn () => AlertContent::make('fancy', 'Message'))
        ->toThrow(InvalidArgumentException::class, 'Alert type [fancy] is not supported.');
});
