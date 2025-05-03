<?php

namespace Workbench\App\Papers;

use Schrojf\Paper\Paper;

class SimplePapers extends Paper
{
    public static string $name = 'Simple paper';
    public static string $description = 'Simple paper';
    public static string $handler = 'simple-paper';

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
