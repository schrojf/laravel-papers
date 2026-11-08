<?php

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Schrojf\Papers\Contents\FormContent;

beforeEach(function () {
    config(['session.driver' => 'array']);
    request()->setLaravelSession(tap(app('session')->driver())->start());
});

test('form content posts to the action with CSRF token', function () {
    $content = FormContent::make('/papers/orders/create')->toHtml();

    expect(csrf_token())->not->toBeEmpty();
    expect($content)->toContain('method="POST"', 'action="/papers/orders/create"')
        ->toContain('name="_token" value="'.csrf_token().'"')
        // A form without buttons can still be submitted.
        ->toContain('>Submit</button>');
});

test('form content renders fields with labels and default values', function () {
    $content = FormContent::make('/action')
        ->text('title', 'Title', 'Report', ['placeholder' => 'Name it', 'required' => true, 'disabled' => false])
        ->number('amount', 'Amount', 5, ['min' => 1])
        ->input('due', 'Due date', 'date', '2026-11-08')
        ->textarea('notes', 'Notes', 'Line one')
        ->select('priority', 'Priority', ['low' => 'Low', 'high' => 'High'], 'high')
        ->checkbox('urgent', 'Urgent', true)
        ->hidden('order', 42)
        ->toHtml();

    expect($content)
        ->toContain('Title', 'type="text"', 'name="title"', 'value="Report"', 'placeholder="Name it" required')
        ->not->toContain('disabled')
        ->toContain('type="number"', 'name="amount"', 'value="5"', 'min="1"')
        ->toContain('type="date"', 'name="due"', 'value="2026-11-08"')
        ->toContain('Notes', '>Line one</textarea>')
        ->toContain('<option value="high" selected>High</option>', '<option value="low" >Low</option>')
        ->toContain('type="checkbox"', 'name="urgent"', 'value="1"')
        ->toContain('type="hidden" name="order" value="42"')
        ->not->toContain('enctype');

    expect($content)->toMatch('/<input\s+type="checkbox"[^>]*\schecked/');
});

test('form content escapes labels, values and attributes', function () {
    $content = FormContent::make('/action?a=1&b=2')
        ->text('title', '<b>Title</b>', '"quoted" & <tagged>', ['placeholder' => '"><script>'])
        ->select('kind', 'Kind', ['<a>' => '<Option>'])
        ->toHtml();

    expect($content)
        ->toContain('action="/action?a=1&amp;b=2"')
        ->toContain('&lt;b&gt;Title&lt;/b&gt;')
        ->toContain('value="&quot;quoted&quot; &amp; &lt;tagged&gt;"')
        ->toContain('placeholder="&quot;&gt;&lt;script&gt;"')
        ->toContain('<option value="&lt;a&gt;" >&lt;Option&gt;</option>')
        ->not->toContain('<script>', '<b>Title</b>');
});

test('form content shows old input and errors of the request', function () {
    session()->flashInput(['title' => 'Old title', 'priority' => 'low', 'items' => ['first']]);
    session()->put('errors', (new ViewErrorBag)->put('default', new MessageBag([
        'title' => ['The title field is too long.'],
        'items.0' => ['The first item is invalid.'],
    ])));

    $content = FormContent::make('/action')
        ->text('title', 'Title', 'Default title')
        ->select('priority', 'Priority', ['low' => 'Low', 'high' => 'High'], 'high')
        ->text('items[0]', 'First item')
        ->checkbox('urgent', 'Urgent', true)
        ->toHtml();

    expect($content)
        ->toContain('value="Old title"', 'The title field is too long.', 'aria-invalid="true"')
        ->not->toContain('Default title')
        ->toContain('<option value="low" selected>Low</option>')
        ->toContain('value="first"', 'The first item is invalid.');

    // The checkbox was not sent, so it was unchecked.
    expect($content)->not->toMatch('/<input\s+type="checkbox"[^>]*\schecked/');
});

test('form content renders submit buttons with names and styles', function () {
    $content = FormContent::make('/action')
        ->submit('Save')
        ->submit('Export', 'intent', 'export', 'secondary')
        ->toHtml();

    expect($content)
        ->toContain('papers-button-primary', '>Save</button>')
        ->toContain('papers-button-secondary', 'name="intent" value="export"', '>Export</button>')
        ->not->toContain('>Submit</button>');
});

test('form content with file input is sent as multipart form data', function () {
    $content = FormContent::make('/action')
        ->input('report', 'Report', 'file')
        ->toHtml();

    expect($content)->toContain('enctype="multipart/form-data"', 'type="file"')
        ->not->toContain('value=""');
});

test('form content asks to confirm it when it has a confirmation message', function () {
    expect(FormContent::make('/action')->confirm('Delete "all" orders?')->toHtml())
        ->toContain('data-papers-confirm="Delete &quot;all&quot; orders?"');

    expect(FormContent::make('/action')->toHtml())
        ->not->toContain('data-papers-confirm');
});

test('form content does not accept input types with methods of their own', function () {
    expect(fn () => FormContent::make('/action')->input('agree', 'Agree', 'checkbox'))
        ->toThrow(InvalidArgumentException::class, 'Input type [checkbox] is not supported.');
});

test('form content does not accept unknown button styles', function () {
    expect(fn () => FormContent::make('/action')->submit('Save', style: 'fancy'))
        ->toThrow(InvalidArgumentException::class, 'Button style [fancy] is not supported.');
});
