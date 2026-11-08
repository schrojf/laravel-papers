<?php

use Schrojf\Papers\Papers;
use Tests\fixtures\Papers\ActionTestPaper;
use Tests\fixtures\Papers\MultiActionTestPaper;
use Tests\fixtures\Papers\SimpleTestPaper;

beforeEach(function () {
    config(['app.name' => 'Papers']);

    Papers::register([
        SimpleTestPaper::class,
        ActionTestPaper::class,
        MultiActionTestPaper::class,
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

test('action name is passed to action method after its dependencies', function () {
    $response = $this->post('/papers/multi-action-test-paper/greet', ['name' => 'John']);

    $response->assertRedirect('/papers/multi-action-test-paper/done');
    $response->assertSessionHas('greeting', 'Hello, John from Papers!');
});

test('result page shown after redirect of action is not repeated by refreshing it', function () {
    $this->followingRedirects()
        ->post('/papers/multi-action-test-paper/greet', ['name' => 'John'])
        ->assertOk()
        ->assertSeeTextInOrder(['Result', 'Hello, John from Papers!']);

    $this->get('/papers/multi-action-test-paper/done')
        ->assertOk()
        ->assertSeeText('Nothing was done.')
        ->assertDontSeeText('Hello, John');
});

test('paper page is rendered by the same instance when named action returns nothing', function () {
    $this->post('/papers/multi-action-test-paper/render')
        ->assertOk()
        ->assertSeeTextInOrder(['Form', 'Rendered', 'Rendered by the render action.']);
});

test('paper decides which action names exist', function () {
    $this->post('/papers/multi-action-test-paper/unknown')->assertNotFound();
});

test('action requiring a name is not found without one', function () {
    $this->post('/papers/multi-action-test-paper')->assertNotFound();
});

test('action method without name parameter has no actions with a name', function () {
    Papers::handlePaperNotFound(function () {
        abort(response('My custom test content.', 418));
    });

    $this->post('/papers/action-test-paper/greet', ['name' => 'John'])->assertStatus(418);
});

test('paper without action method does not accept POST requests with action name', function () {
    $this->post('/papers/simple-test-paper/greet')->assertStatus(405);
});

test('form shows old input and errors after named action fails validation', function () {
    $response = $this->from('/papers/multi-action-test-paper')
        ->followingRedirects()
        ->post('/papers/multi-action-test-paper/greet', ['name' => 'Bartholomew']);

    $response->assertOk();
    $response->assertSee('action="http://localhost/papers/multi-action-test-paper/greet"', false);
    $response->assertSee('value="Bartholomew"', false);
    $response->assertSeeText('The name field must not be greater than 10 characters.');
    // The checkbox checked by default was not sent, so it is unchecked like the user left it.
    expect($response->getContent())->not->toMatch('/<input\s+type="checkbox"[^>]*\schecked/');
    expect($this->get('/papers/multi-action-test-paper')->getContent())->toMatch('/<input\s+type="checkbox"[^>]*\schecked/');
});
