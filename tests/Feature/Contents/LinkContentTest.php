<?php

use Schrojf\Papers\Contents\LinkContent;

test('link content is rendered', function () {
    $content = LinkContent::make('Orders & <more>', '/papers/orders?status=open&sort=name')->toHtml();

    expect($content)
        ->toContain('href="/papers/orders?status=open&amp;sort=name"')
        ->toContain('>Orders &amp; &lt;more&gt;</a>')
        ->toContain('text-blue-600')
        ->not->toContain('papers-button');
});

test('link content can look like a button', function () {
    expect(LinkContent::make('Back', '/papers')->button()->toHtml())->toContain('papers-button papers-button-secondary')
        ->and(LinkContent::make('Back', '/papers')->button('primary')->toHtml())->toContain('papers-button papers-button-primary');

    expect(fn () => LinkContent::make('Back', '/papers')->button('fancy'))
        ->toThrow(InvalidArgumentException::class, 'Button style [fancy] is not supported.');
});
