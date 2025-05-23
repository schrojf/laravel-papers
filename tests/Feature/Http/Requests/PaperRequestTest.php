<?php

use Illuminate\Foundation\Application;
use Schrojf\Papers\Http\Requests\PaperRequest;
use Schrojf\Papers\Papers;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\fixtures\Papers\SimpleTestPaper;

test('paper class is resolved from request by handler', function () {
    Papers::register([
        SimpleTestPaper::class,
    ]);

    $response = $this->get('/papers/simple-test-paper');
    $request = PaperRequest::createFrom($response->baseRequest);

    expect($request->paper())->toBe(SimpleTestPaper::class);
})->skip(
    version_compare(Application::VERSION, '11', '<'),
    'test requires Laravel 11 or higher due to changes in request handling'
);

test('not found exception is thrown when handler not found', function () {
    Papers::register([
        SimpleTestPaper::class,
    ]);

    $response = $this->get('/papers/non-existing-paper');
    $request = PaperRequest::createFrom($response->baseRequest);

    expect(fn () => $request->paper())->toThrow(NotFoundHttpException::class);
})->skip(
    version_compare(Application::VERSION, '11', '<'),
    'test requires Laravel 11 or higher due to changes in request handling'
);

test('custom not found handler when paper class could not been resolved', function () {
    Papers::handlePaperNotFound(function () {
        throw new RuntimeException('My custom test error.');
    });

    $response = $this->get('/papers/non-existing-handler');
    $request = PaperRequest::createFrom($response->baseRequest);

    expect(fn () => $request->paper())->toThrow(RuntimeException::class, 'My custom test error.');
})->skip(
    version_compare(Application::VERSION, '11', '<'),
    'test requires Laravel 11 or higher due to changes in request handling'
);
