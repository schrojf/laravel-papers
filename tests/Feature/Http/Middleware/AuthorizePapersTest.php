<?php

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Schrojf\Papers\Http\Middleware\AuthorizePapers;

beforeEach(function () {
    Route::middleware([AuthorizePapers::class])->get('/papers-test', fn () => response('OK'));
});

afterEach(function () {
    Mockery::close();
});

function mockUser(): Authenticatable
{
    $user = Mockery::mock(Authenticatable::class);
    $user->shouldReceive('getAuthIdentifier')->andReturn(1);

    return $user;
}

it('default forbids access', function () {
    expect(Gate::has('viewPapers'))->toBeFalse();

    $this->get('/papers-test')->assertForbidden();
});

it('allows access when gate permits', function () {
    Config::set('papers.guard', 'web');

    $user = mockUser();

    Gate::define('viewPapers', fn ($authUser) => true);

    $this->be($user, 'web');

    $this->get('/papers-test')->assertOk();
});

it('forbids access when gate denies', function () {
    Config::set('papers.guard', 'web');

    $user = mockUser();

    Gate::define('viewPapers', fn ($authUser) => false);

    $this->be($user, 'web');

    $this->get('/papers-test')->assertForbidden();
});

it('uses fallback guard when papers.guard is not set', function () {
    Config::set('papers.guard', null);
    Config::set('auth.defaults.guard', 'web');

    $user = mockUser();

    Gate::define('viewPapers', fn ($authUser) => true);

    $this->be($user, 'web');

    $this->get('/papers-test')->assertOk();
});
