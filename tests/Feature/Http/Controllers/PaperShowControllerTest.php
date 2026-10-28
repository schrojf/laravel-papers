<?php

use Illuminate\Support\Str;
use Schrojf\Papers\Papers;
use Tests\fixtures\Papers\SimpleTestPaper;

test('non-existing paper', function () {
    $response = $this->get('/papers/handler');

    $response->assertNotFound();
});

test('paper class is resolved from request by handler', function () {
    Papers::register([
        SimpleTestPaper::class,
    ]);

    $response = $this->get('/papers/simple-test-paper');

    $response->assertOk();
    $response->assertSeeTextInOrder([
        SimpleTestPaper::name(),
        SimpleTestPaper::description(),
    ]);
});

test('custom not found handler', function () {
    Papers::handlePaperNotFound(function () {
        abort(response('My custom test content.', 418));
    });

    $response = $this->get('/papers/non-existing-handler');

    $response->assertStatus(418);
    $response->assertContent('My custom test content.');
});

test('paper page has filter for papers in navigation', function () {
    Papers::register([
        SimpleTestPaper::class,
    ]);

    $response = $this->get('/papers/simple-test-paper');

    $response->assertOk()
        ->assertSee('aria-controls="papers-nav-list"', false)
        ->assertDontSee('aria-controls="papers-index-list"', false);
});

test('paper page has favicon and head HTML in its head', function () {
    Papers::register([
        SimpleTestPaper::class,
    ]);

    Papers::favicon('/favicon-green.png');
    Papers::head(fn () => '<link rel="manifest" href="/papers.webmanifest">');

    $response = $this->get('/papers/simple-test-paper');

    $response->assertOk();

    expect(Str::between($response->getContent(), '<head>', '</head>'))
        ->toContain('<link rel="icon" href="/favicon-green.png">')
        ->toContain('<link rel="manifest" href="/papers.webmanifest">');
});
