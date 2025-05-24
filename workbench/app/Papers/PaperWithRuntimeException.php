<?php

namespace App\Papers;

use RuntimeException;
use Schrojf\Papers\Paper;

class PaperWithRuntimeException extends Paper
{
    public function sections(): array
    {
        return [
            'Intro' => function () {
                return 'Next section will raise an exception and Report section will not be ran.';
            },

            'Section with an Exception' => function () {
                throw new RuntimeException('This is an exception.');
            },

            'Report' => function () {
                return 'This section will never be executed.';
            },
        ];
    }
}
