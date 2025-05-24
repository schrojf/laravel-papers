<?php

namespace App\Papers;

use Illuminate\Support\HtmlString;
use Schrojf\Papers\Contents\TableContent;
use Schrojf\Papers\Paper;

class HeavyStressTableContentTest extends Paper
{
    public function sections(): array
    {
        return [

            'Intro' => function () {
                return <<<'EOT'
This paper generates a heavy stress test table with 120 rows containing diverse and complex content,
including long text, emojis, nested tables, HTML elements, interactive buttons, RTL text, and more.
It's designed to thoroughly test the table layout’s robustness, responsiveness, and rendering capabilities
under extreme and varied data conditions.
EOT;
            },

            'Heavy Stress Test' => function () {
                $headers = [
                    'ID', 'Description', 'Status', 'Δ Value', '☯ Metric',
                    'VeryLongColumnNameThatWillOverflow', '% Complete', 'Last Updated', '🤯 Note',
                ];

                $statuses = [
                    new HtmlString('<span style="color:#15803d;">ACTIVE</span>'),       // green-700
                    new HtmlString('<span style="color:#78350f;">pending</span>'),      // yellow-700
                    new HtmlString('<span style="color:#b91c1c;">ERROR</span>'),        // red-700
                    new HtmlString('<span style="color:#4b5563;">archived</span>'),     // gray-700
                    new HtmlString('<span style="color:#6b21a8;">complex</span>'),      // purple-700
                ];

                $emojis = ['🚀', '🔥', '🐍', '🧠', '✨', '🎉', '🐱‍👤', '💡', '⚡️', '❌', '✅', '☯', '∞'];
                $metrics = ['∞', '42', '∑x²', 'π', '✓', '!!', 'N/A'];

                $longTexts = [
                    'ThisIsJustWayTooLongToBeReasonableButStillHere',
                    'AbsolutelyNoWayThisShouldRenderPerfectlyButItWill',
                    'MixedContentWith🐍UnicodeAnd🚫HTML',
                    'Readable-BUT_Slightly_Annoying_12345',
                    'ExtremelyLongTextWithoutSpacesToTestOverflowBehaviorAndScrollingCapabilitiesAtMaximumLevel',
                ];

                $randomDescriptionSamples = [
                    'Basic 🔧 initialization & configuration module',
                    'μService connector → pipeline (β release)',
                    'CRITICAL: Overrun @ memory.block[5]!',
                    'Whitespace   handling  check',
                    'שלום עולם (Hello world in Hebrew) 🌍',
                    '🎉🚀🔥✨🐍🍕🎮🎵💡🧠',
                    'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',
                ];

                $notesSamples = [
                    '"🚀 Fast-track enabled — no breaks!"',
                    '"👀 Eyes on this — flaky!"',
                    '"🔥 CRASH detected. See logs. 😱"',
                    '"🧪 Legacy test artifact."',
                    '"Nested table inside a cell — madness! 🚀"',
                    '"Try clicking buttons & toggles!"',
                    '"Emoji overload!!! 🤯🤪😵‍💫"',
                ];

                $rows = [];

                for ($i = 1; $i <= 120; $i++) {
                    $status = $statuses[array_rand($statuses)];
                    $metric = $metrics[array_rand($metrics)];
                    $longText = $longTexts[array_rand($longTexts)];
                    $desc = $randomDescriptionSamples[array_rand($randomDescriptionSamples)];
                    $note = $notesSamples[array_rand($notesSamples)];

                    $description = $desc;

                    if (rand(1, 10) === 1) {
                        // Nested mini table styled inline
                        $description = new HtmlString(
                            '<table style="min-width:100%; border-collapse: collapse; border: 1px solid #9ca3af;">'.
                            '<thead><tr><th style="border: 1px solid #9ca3af; padding: 4px;">SubID</th><th style="border: 1px solid #9ca3af; padding: 4px;">Val</th></tr></thead>'.
                            '<tbody>'.
                            '<tr><td style="border: 1px solid #9ca3af; padding: 4px;">1A</td><td style="border: 1px solid #9ca3af; padding: 4px;">42</td></tr>'.
                            '<tr><td style="border: 1px solid #9ca3af; padding: 4px;">1B</td><td style="border: 1px solid #9ca3af; padding: 4px;">∞</td></tr>'.
                            '</tbody></table>'
                        );
                    } elseif (rand(1, 20) === 1) {
                        // Interactive button styled inline
                        $description = new HtmlString(
                            '<button style="'.
                            'background-color:#3b82f6; color:#fff; padding:4px 8px; border:none; border-radius:4px; cursor:pointer;'.
                            '" onmouseover="this.style.backgroundColor=\'#2563eb\'" onmouseout="this.style.backgroundColor=\'#3b82f6\'">'.
                            'Click me</button>'
                        );
                    }

                    $deltaValue = (rand(0, 1) ? '+' : '-').number_format(rand(0, 999999) / 1000, 5);

                    $veryLongCol = $longText.' '.implode('', array_rand(array_flip($emojis), 3));

                    $percentComplete = number_format(rand(0, 10000) / 100, 2).'%';

                    $timestamp = mt_rand(strtotime('2025-01-01'), strtotime('2025-12-31'));
                    $lastUpdated = date('Y-m-d H:i:s \U\T\C', $timestamp);

                    if (rand(1, 10) === 1) {
                        $note = '';
                    } elseif (rand(1, 10) === 2) {
                        $note = new HtmlString('&nbsp;&nbsp;');
                    } else {
                        $note = new HtmlString($note);
                    }

                    $rows[] = [
                        '#'.str_pad($i, 3, '0', STR_PAD_LEFT),
                        $description,
                        $status,
                        $deltaValue,
                        $metric,
                        $veryLongCol,
                        $percentComplete,
                        $lastUpdated,
                        $note,
                    ];
                }

                $table = TableContent::withHeaders($headers);
                foreach ($rows as $row) {
                    $table->row($row);
                }

                return $table;
            },

            'Outro' => function () {
                return 'This concludes the heavy stress test of the table component. Feel free to adjust the data volume or complexity to fit your testing needs.';
            },

        ];
    }
}
