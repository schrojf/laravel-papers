<?php

use Schrojf\Papers\Papers;
use Tests\fixtures\Papers\EmptyTestPaper;
use Tests\fixtures\Papers\SimpleTestPaper;

test('empty page is shown', function () {
    $response = $this->get('/papers');

    $response->assertStatus(200)
        ->assertSeeText('No paper class is registered');
});

test('index page is shown with registered paper', function () {
    Papers::register([
        SimpleTestPaper::class,
    ]);

    $response = $this->get('/papers');

    $response->assertStatus(200)
        ->assertSeeTextInOrder([
            SimpleTestPaper::name(),
            'No paper class is selected',
            SimpleTestPaper::name(),
            SimpleTestPaper::description(),
        ]);
});

test('index page shows all registered papers in order', function () {
    Papers::register([
        SimpleTestPaper::class,
    ]);

    Papers::register([
        EmptyTestPaper::class,
    ]);

    $response = $this->get('/papers');

    $response->assertStatus(200)
        ->assertSeeTextInOrder([
            SimpleTestPaper::name(),
            EmptyTestPaper::name(),
            'No paper class is selected',
            SimpleTestPaper::name(),
            SimpleTestPaper::description(),
            EmptyTestPaper::name(),
        ]);
});
