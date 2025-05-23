<?php

namespace App\Papers;

use Schrojf\Papers\Paper;

class EmptyPaper extends Paper
{
    public static $description = 'Paper with no sections.';

    public function sections(): array
    {
        return [];
    }
}
