<?php

use Schrojf\Papers\Papers;
use Tests\fixtures\Papers\SimpleTestPaper;

afterEach(function () {
    Papers::replacePapers([]);
});

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
})->after(function () {
    Papers::replacePapers([]);
});

test('custom not found handler', function () {
    Papers::handlePaperNotFound(function () {
        abort(response('My custom test content.', 418));
    });

    $response = $this->get('/papers/non-existing-handler');

    $response->assertStatus(418);
    $response->assertContent('My custom test content.');
})->after(function () {
    Papers::handlePaperNotFound(null);
});
