<?php

namespace Tests\fixtures\Papers;

use RuntimeException;
use Schrojf\Papers\Paper;

class WithExceptionTestPaper extends Paper
{
    public static $description = 'Test paper which will raise exception in one of its cells.';

    public function sections(): array
    {
        return [

            'First section' => function () {
                return 'OK';
            },

            'Second section' => function () {
                throw new RuntimeException('Test paper which will raise an exception.');
            },

            'Third section' => function () {
                return 'Three';
            },

        ];
    }
}
