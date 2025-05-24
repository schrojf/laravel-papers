<?php

namespace App\Papers;

use Illuminate\Support\HtmlString;
use Schrojf\Papers\Contents\TableContent;
use Schrojf\Papers\Paper;

class TableContentPaper extends Paper
{
    public function sections(): array
    {
        return [

            'Intro' => function () {
                return 'This paper demonstrates a simple responsive table component. It shows how to render tabular data with headers, including support for empty cells and multiple data types, useful for layout testing and UI development.';
            },

            'Simple table' => function () {
                return TableContent::withHeaders(['ID', 'Name', 'Status', 'Score'])
                    ->row([1, 'Alice', 'Active', 85])
                    ->row([2, 'Bob', null, 92])
                    ->row([3, 'Charlie', 'Inactive', null])
                    ->row([4, null, 'Active', 78]);
            },

            'Long content' => function () {
                return TableContent::withHeaders(['Index', 'Description', 'HTML Content', 'Notes'])
                    ->row([
                        1,
                        'Short text',
                        new HtmlString('<strong>Bold HTML</strong>'),
                        'Normal text',
                    ])
                    ->row([
                        2,
                        'Very very long text that should wrap or overflow the cell to test responsiveness and layout behavior in various screen sizes.',
                        new HtmlString('<a href="https://example.com" target="_blank">Example link</a>'),
                        null,
                    ])
                    ->row([
                        3,
                        null,
                        new HtmlString('<ul><li>List item 1</li><li>List item 2 with <em>emphasis</em></li></ul>'),
                        'Contains a list',
                    ])
                    ->row([
                        4,
                        'Special chars: < > & " \' / \\',
                        new HtmlString('<img src="https://placehold.co/60" alt="Icon" />'),
                        'Image tag',
                    ])
                    ->row([
                        5,
                        'Mixed content with numbers 1234567890 and symbols !@#$%^&*()',
                        new HtmlString('<div style="color:red;">Red text block</div>'),
                        new HtmlString('<code>code snippet()</code>'),
                    ])
                    ->row([
                        6,
                        new HtmlString('<em>Italic content</em> with <strong>bold</strong> and <u>underline</u>'),
                        null,
                        'Null in HTML Content column',
                    ]);
            },

            'Stress test' => function () {
                return TableContent::withHeaders([
                    'ID',
                    'Description',
                    'Status',
                    'Δ Value',
                    '☯ Metric',
                    'VeryLongColumnNameThatWillOverflow',
                    '% Complete',
                    'Last Updated',
                    '🤯 Note',
                ])
                    ->row([
                        '#001',
                        new HtmlString('Basic 🔧 initialization & configuration module'),
                        new HtmlString('<span style="color: #15803d;">ACTIVE</span>'),
                        '+123.456',
                        new HtmlString('∞'),
                        'ThisIsJustWayTooLongToBeReasonableButStillHere',
                        '99.99%',
                        '2025-05-23 17:34:11 UTC',
                        new HtmlString('"🚀 Fast-track enabled — no breaks!"'),
                    ])
                    ->row([
                        '#002',
                        new HtmlString('μService connector → pipeline (β release)'),
                        new HtmlString('<span style="color: #a16207;">pending</span>'),
                        '-0.00001',
                        '42',
                        'AbsolutelyNoWayThisShouldRenderPerfectlyButItWill',
                        '4.20%',
                        '2025-05-22 06:20:00 UTC',
                        new HtmlString('"👀 Eyes on this — flaky!"'),
                    ])
                    ->row([
                        '#003',
                        new HtmlString('CRITICAL: Overrun @ memory.block[5]!'),
                        new HtmlString('<span style="color: #b91c1c;">ERROR</span>'),
                        '-9999999.99',
                        new HtmlString('∑x²'),
                        'MixedContentWith🐍UnicodeAnd🚫HTML',
                        '0.00%',
                        '2025-05-20 01:01:01 UTC',
                        new HtmlString('"🔥 CRASH detected. See logs. 😱"'),
                    ])
                    ->row([
                        '#004',
                        new HtmlString('Whitespace&nbsp;&nbsp;&nbsp;handling  check'),
                        new HtmlString('<span style="color: #374151;">archived</span>'),
                        '0',
                        new HtmlString('π'),
                        'Readable-BUT_Slightly_Annoying_12345',
                        '50%',
                        '2025-05-10 12:00:00 UTC',
                        new HtmlString('"🧪 Legacy test artifact."'),
                    ]);
            },

        ];
    }
}
