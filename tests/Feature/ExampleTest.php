<?php

it('can test', function () {
    expect(true)->toBeTrue();
});

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
