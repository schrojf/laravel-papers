<?php

namespace Tests\fixtures\Papers;

use Schrojf\Papers\Paper;

class EmptyTestPaper extends Paper
{
    public function sections(): array
    {
        return [];
    }
}
