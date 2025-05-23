<?php

namespace App\Papers;

use Illuminate\Support\HtmlString;
use Schrojf\Papers\Paper;

class SimplePaper extends Paper
{
    public static $description = 'Simple paper description.';

    public function sections(): array
    {
        return [
            'first' => function () {
                return 'Hello World!';
            },

            'empty' => function () {
                return null;
            },

            'second' => function () {
                $rand = rand(1, 100);

                return 'Random number: '.$rand;
            },

            'third' => function () {
                return [
                    'Regular text',
                    new HtmlString('<p style="background: aqua; color: blue;">Paragraph</p>'),
                    '<strong>Text</strong>',
                ];
            },
        ];
    }
}
