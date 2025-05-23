<?php

namespace App\Http\Controllers;

use App\Papers\SimplePaper;
use Illuminate\Http\Request;
use RuntimeException;
use Schrojf\Papers\Contents\ExceptionContent;

class DummyPaperContentController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $paper = SimplePaper::class;

        $content = [
            [
                'type' => 'section',
                'name' => 'Content section',
                'content' => ['first', 'second', 'third'],
            ],
            [
                'type' => 'section',
                'name' => 'Empty #1',
                'content' => [],
            ],
            [
                'type' => 'section',
                'name' => 'Empty #2',
                'content' => null,
            ],
            [
                'type' => 'section',
                'name' => 'Empty #3',
            ],
            [
                'type' => 'whatever',
                'name' => 'Unknown type',
            ],
            [
                'type' => 'section',
                'name' => 'Exception',
                'content' => [
                    'Next content will be rendered as exception:',
                    new ExceptionContent(new RuntimeException('Something bad happened.')),
                ],
            ],
            [
                'type' => 'error',
                'content' => ['Form validation', 'Data processing', 'Email notification'],
            ]
        ];

        return view('papers::pages.paper', [
            'papers' => null,
            'paper' => $paper,
            'content' => $content,
        ]);
    }
}
