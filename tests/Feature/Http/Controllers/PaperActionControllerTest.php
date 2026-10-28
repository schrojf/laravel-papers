<?php

use Schrojf\Papers\Papers;
use Tests\fixtures\Papers\ActionTestPaper;
use Tests\fixtures\Papers\SimpleTestPaper;

beforeEach(function () {
    config(['app.name' => 'Papers']);

    Papers::register([
        SimpleTestPaper::class,
        ActionTestPaper::class,
    ]);
});

test('non-existing paper', function () {
    $response = $this->post('/papers/handler');

    $response->assertNotFound();
});

test('paper without action method does not accept POST requests', function () {
    $response = $this->post('/papers/simple-test-paper');

    $response->assertStatus(405);
    $response->assertHeader('Allow', 'GET, HEAD');
});

test('action is not handled by GET request', function () {
    $response = $this->get('/papers/action-test-paper?name=John');

    $response->assertOk();
    $response->assertSeeText('No action was handled.');
    $response->assertDontSeeText('Hello');
});

test('paper is rendered by the same instance when action returns nothing', function () {
    $response = $this->post('/papers/action-test-paper', ['name' => 'John']);

    $response->assertOk();
    $response->assertSeeTextInOrder([
        ActionTestPaper::name(),
        'Greeting',
        'Hello, John from Papers!',
    ]);
    $response->assertDontSeeText('No action was handled.');
});

test('response returned by action is sent', function () {
    $this->post('/papers/action-test-paper', ['name' => 'John', 'respond' => 'redirect'])
        ->assertRedirect('/greeted');

    $this->post('/papers/action-test-paper', ['name' => 'John', 'respond' => 'json'])
        ->assertOk()
        ->assertExactJson(['greeting' => 'Hello, John from Papers!']);
});

test('invalid action request redirects back with errors', function () {
    $response = $this->from('/papers/action-test-paper')->post('/papers/action-test-paper');

    $response->assertRedirect('/papers/action-test-paper');
    $response->assertSessionHasErrors('name');
});

test('action requires CSRF token', function () {
    // CSRF tokens are not verified while running unit tests.
    $this->app['env'] = 'local';

    $response = $this->post('/papers/action-test-paper', ['name' => 'John']);

    $response->assertStatus(419);
});
