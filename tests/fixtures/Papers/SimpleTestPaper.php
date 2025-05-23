<?php

namespace Tests\fixtures\Papers;

use Schrojf\Papers\Paper;

class SimpleTestPaper extends Paper
{
    public static $description = 'Simple test paper description.';

    public function sections(): array
    {
        return [
            'First section' => function () {
                return 'One';
            },

            'Second section' => function () {
                return null;
            },

            'Third section' => function () {
                return ['Two', 'Three'];
            },
        ];
    }
}
