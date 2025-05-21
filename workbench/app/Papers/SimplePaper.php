<?php

namespace App\Papers;

use Schrojf\Papers\Paper;

class SimplePaper extends Paper
{
    public static $description = 'Simple paper';

    public function definition(): array
    {
        return [
            'first' => function () {
                echo 'Hello';
            },

            'second' => function () {
                $rand = rand(1, 100);

                return $rand;
            },

            'third' => function ($arg) {
                echo 'Random number: '.$arg;
            },
        ];
    }
}
