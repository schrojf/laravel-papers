<?php

namespace Tests\fixtures\Papers;

use Schrojf\Papers\Paper;

class FullWidthTestPaper extends Paper
{
    public static $fullWidth = true;

    public function sections(): array
    {
        return [
            'First section' => function () {
                return 'One';
            },
        ];
    }
}
