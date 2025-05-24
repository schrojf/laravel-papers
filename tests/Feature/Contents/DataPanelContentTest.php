<?php

use Schrojf\Papers\Contents\DataPanelContent;

test('data panel content is rendered', function () {
    $content = DataPanelContent::make()
        ->title('Title')
        ->item('Item 1', 'Value 1')
        ->item('Item 2', 'Value 2')
        ->toHtml();

    expect($content)->toContain('Title')
        ->toContain('Item 1', 'Value 1')
        ->toContain('Item 2', 'Value 2');
});
