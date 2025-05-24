<?php

namespace App\Papers;

use Illuminate\Support\HtmlString;
use Schrojf\Papers\Contents\DataPanelContent;
use Schrojf\Papers\Paper;

class DataPanelContentPaper extends Paper
{
    public function sections(): array
    {
        return [

            'Intro' => function () {
                return new HtmlString(<<<'HTML'
        <div style="font-family: sans-serif; line-height: 1.5;">
            <p>This paper demonstrates the usage and layout behavior of the <code>DataPanelContent</code> component.</p>
            <ul style="margin-top: 0.75em; padding-left: 1.25em;">
                <li>Verifies correct rendering of key-value content blocks.</li>
                <li>Includes both standard and edge-case test data.</li>
                <li>Helps developers ensure consistent visual output across data types.</li>
                <li>Tests layout responsiveness with long text, special characters, Unicode, multiline values, and raw HTML.</li>
            </ul>
            <p style="margin-top: 1em; font-size: 0.9em; color: #555;">
                Feel free to expand this panel with additional tests as new layout features or content formats are introduced.
            </p>
        </div>
    HTML);
            },

            'Sample Usage with Dummy Data' => function () {
                return DataPanelContent::make()
                    ->title('User Profile')
                    ->item('Name', 'Jane Doe')
                    ->item('Email', 'jane.doe@example.com')
                    ->item('Phone', '+1 234 567 8901')
                    ->item('Role', 'Administrator')
                    ->item('Status', 'Active');
            },

            '🧪 Stress Test Example' => function () {
                return DataPanelContent::make()
                    ->title('🌍 Complex Profile: Ævar Örn Guðmundsson — 数据测试')

                    ->item('🌐 Full Name', 'Ævar Örn Guðmundsson')
                    ->item('📧 Email', 'ævar.g@example.is')
                    ->item('💬 Status', '🟢 Online — Responds within 5 mins')
                    ->item('📄 Bio', "Lorem ipsum dolor sit amet,\nconsectetur adipiscing elit.\nSed do eiusmod tempor incididunt ut labore.")

                    ->item('📦 Favorite Unicode', '🐍 → 🧙‍♂️ → 🪄 → ✨')
                    ->item('📚 Languages', '🇯🇵 日本語, 🇨🇳 中文, 🇰🇷 한국어')

                    ->item('📊 HTML Content', new HtmlString('<span class="text-green-600 font-semibold">42% complete</span>'))
                    ->item('📅 Multiline HTML', new HtmlString('<div><strong>Start:</strong> Jan 1<br><strong>End:</strong> Dec 31</div>'))

                    ->item('🔢 Numbers', '1,234,567.89')
                    ->item('🔗 External Link', new HtmlString('<a href="https://example.com" class="text-blue-600 underline" target="_blank">Visit Profile</a>'))
                    ->item('🧪 Long Key', 'This key is exceptionally, unnecessarily, even absurdly long just to see what happens to the layout responsiveness')
                    ->item('🧾 Long Value', str_repeat('This is a very long string. ', 10))

                    ->item('🧬 HTML Entities', '&amp; &lt; &gt; &quot; &#169; &#x1F4A1;')
                    ->item('🗃️ JSON Example', json_encode(['key' => 'value', 'arr' => [1, 2, 3], 'bool' => true], JSON_PRETTY_PRINT))
                    ->item('🌀 Raw HTML tags', '<script>alert("xss")</script>'); // this will show as plain text unless escaped
            },

        ];
    }
}
