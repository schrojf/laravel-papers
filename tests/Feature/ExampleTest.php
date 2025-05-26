<?php

use Illuminate\Support\Facades\Schema;

it('can test', function () {
    expect(true)->toBeTrue();
});

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('package is aware of console command', function () {
    $this->artisan('papers')->assertExitCode(0);
});

test('package is aware of migration file', function () {
    expect(Schema::hasTable('papers'))->toBeTrue();
});
