<?php

namespace Tests\fixtures\Papers;

use Schrojf\Papers\Paper;

class PagesTestPaper extends Paper
{
    public function sections(?string $page = null): array
    {
        return match ($page) {
            null => ['Home' => fn () => 'The main page.'],
            'details' => ['Details' => fn () => 'The details page.'],
            default => abort(404),
        };
    }
}
