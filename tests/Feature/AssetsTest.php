<?php

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Foundation\Vite as FoundationVite;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Schrojf\Papers\Papers;
use Schrojf\Papers\PapersServiceProvider;

beforeEach(function () {
    $this->originalHotFile = Papers::$hotFile;
    $this->publicPath = sys_get_temp_dir().'/papers-test-'.getmypid();

    Papers::$hotFile = $this->publicPath.'/papers.hot';
    $this->app->usePublicPath($this->publicPath);
});

afterEach(function () {
    Papers::$hotFile = $this->originalHotFile;

    File::deleteDirectory($this->publicPath);
});

function publishAssets(): array
{
    File::copyDirectory(__DIR__.'/../../dist', public_path(Papers::BUILD_DIRECTORY));

    return json_decode(File::get(public_path(Papers::BUILD_DIRECTORY.'/manifest.json')), true);
}

function runHot(): void
{
    File::ensureDirectoryExists(dirname(Papers::$hotFile));
    File::put(Papers::$hotFile, 'http://localhost:5173');
}

test('css is inlined in a style tag', function () {
    $css = Papers::css();

    expect($css)->toBeInstanceOf(Htmlable::class)
        ->and($css->toHtml())->toStartWith('<style>')->toEndWith('</style>');
});

test('js is inlined in a module script tag with script variables', function () {
    $js = Papers::js();

    expect($js)->toBeInstanceOf(Htmlable::class)
        ->and($js->toHtml())
        ->toStartWith('<script type="module">')
        ->toContain('window.Papers = {};')
        ->toEndWith('</script>');
});

test('inline tags use the vite csp nonce', function () {
    Vite::useCspNonce('test-nonce');

    expect(Papers::css()->toHtml())->toStartWith('<style nonce="test-nonce">')
        ->and(Papers::js()->toHtml())->toStartWith('<script type="module" nonce="test-nonce">');
});

test('published assets are loaded from the public path', function () {
    $manifest = publishAssets();

    expect(Papers::assetsAreCurrent())->toBeTrue()
        ->and(Papers::css()->toHtml())
        ->toContain('<link rel="stylesheet" href="'.asset(Papers::BUILD_DIRECTORY.'/'.$manifest['resources/css/papers.css']['file']).'"')
        ->and(Papers::js()->toHtml())
        ->toStartWith('<script>window.Papers = ')
        ->toContain('<script type="module" src="'.asset(Papers::BUILD_DIRECTORY.'/'.$manifest['resources/js/papers.js']['file']).'"');
});

test('outdated published assets fall back to inline assets', function () {
    publishAssets();

    File::put(public_path(Papers::BUILD_DIRECTORY.'/manifest.json'), '{}');

    expect(Papers::assetsAreCurrent())->toBeFalse()
        ->and(Papers::css()->toHtml())->toStartWith('<style>')
        ->and(Papers::js()->toHtml())->toStartWith('<script type="module">');
});

test('assets are loaded from the vite development server while running hot', function () {
    runHot();

    expect(Papers::css()->toHtml())
        ->toContain('src="http://localhost:5173/@vite/client"')
        ->toContain('href="http://localhost:5173/resources/css/papers.css"')
        ->and(Papers::js()->toHtml())
        ->toContain('src="http://localhost:5173/resources/js/papers.js"');
});

test('vite tags use the vite csp nonce', function () {
    Vite::useCspNonce('test-nonce');

    publishAssets();

    expect(Papers::css()->toHtml())->toContain('<link rel="stylesheet" href="')->toContain(' nonce="test-nonce"')
        ->and(Papers::js()->toHtml())->toStartWith('<script nonce="test-nonce">')->toContain(' nonce="test-nonce"></script>');
});

test('application vite configuration is not changed', function () {
    runHot();

    Papers::css();

    expect(app(FoundationVite::class)->hotFile())->toBe(public_path('/hot'));
});

test('assets are published with the laravel-assets tag', function () {
    $paths = ServiceProvider::pathsToPublish(PapersServiceProvider::class, 'laravel-assets');

    expect($paths)->toHaveCount(1)
        ->and(realpath(array_key_first($paths)))->toBe(realpath(__DIR__.'/../../dist'))
        ->and(reset($paths))->toEndWith('/'.Papers::BUILD_DIRECTORY);
});

test('papers pages render inline tags unescaped', function () {
    $this->get('/papers')
        ->assertOk()
        ->assertSee('<style>', false)
        ->assertSee('<script type="module">', false);
});
