<?php

use Schrojf\Papers\Contents\ButtonContent;

test('button content posts to the action with CSRF token and hidden values', function () {
    config(['session.driver' => 'array']);
    request()->setLaravelSession(tap(app('session')->driver())->start());

    $content = ButtonContent::make('Delete', '/papers/orders/delete')
        ->hidden('order', 42)
        ->hidden('note', '"x" & <y>')
        ->toHtml();

    expect($content)
        ->toContain('method="POST"', 'action="/papers/orders/delete"')
        ->toContain('name="_token" value="'.csrf_token().'"')
        ->toContain('type="hidden" name="order" value="42"')
        ->toContain('value="&quot;x&quot; &amp; &lt;y&gt;"')
        ->toContain('papers-button-secondary', '>Delete</button>')
        ->not->toContain('data-papers-confirm');
});

test('button content has styles', function () {
    expect(ButtonContent::make('Save', '/action')->primary()->toHtml())->toContain('papers-button-primary')
        ->and(ButtonContent::make('Delete', '/action')->danger()->toHtml())->toContain('papers-button-danger')
        ->and(ButtonContent::make('Delete', '/action')->style('secondary')->toHtml())->toContain('papers-button-secondary');

    expect(fn () => ButtonContent::make('Save', '/action')->style('fancy'))
        ->toThrow(InvalidArgumentException::class, 'Button style [fancy] is not supported.');
});

test('button content asks to confirm the action', function () {
    expect(ButtonContent::make('Delete', '/action')->confirm('Delete <all>?')->toHtml())
        ->toContain('data-papers-confirm="Delete &lt;all&gt;?"');
});
