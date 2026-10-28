<?php

use Schrojf\Papers\Papers;
use Tests\fixtures\Papers\EmptyTestPaper;
use Tests\fixtures\Papers\SearchableTestPaper;
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

test('index page and navigation have filters for papers', function () {
    Papers::register([
        SimpleTestPaper::class,
        SearchableTestPaper::class,
    ]);

    $response = $this->get('/papers');

    $response->assertOk()
        ->assertSee('aria-controls="papers-nav-list"', false)
        ->assertSee('aria-controls="papers-index-list"', false);

    // Read the search data the way the browser does, so quotes or markup in it can't break the attribute.
    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
    $xpath = new DOMXPath($document);

    foreach (['papers-nav-list', 'papers-index-list'] as $list) {
        $searchData = array_map(
            fn (DOMElement $item) => json_decode($item->getAttribute('data-paper'), true),
            iterator_to_array($xpath->query("//ul[@id='{$list}']/li"))
        );

        expect($searchData)->toBe([
            SimpleTestPaper::searchData(),
            SearchableTestPaper::searchData(),
        ]);
    }
});
