<?php

use Illuminate\Contracts\Support\Htmlable;
use Schrojf\Papers\Contents\Content;

test('content class', function () {
    expect(is_subclass_of(Content::class, Htmlable::class))->toBeTrue();
});
