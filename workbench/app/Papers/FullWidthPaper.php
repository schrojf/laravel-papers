<?php

namespace App\Papers;

use Schrojf\Papers\Contents\TableContent;
use Schrojf\Papers\Paper;

class FullWidthPaper extends Paper
{
    public static $description = 'Paper using the full width of the screen, with tables fitted to the width of their sections.';

    public static $fullWidth = true;

    public function sections(): array
    {
        return [
            'Fitted table' => fn () => $this->wideTable()->fit(),

            'Table without fit' => fn () => $this->wideTable(),

            'Fitted narrow table' => fn () => TableContent::withHeaders(['ID', 'Name'])
                ->row([1, 'Alice'])
                ->row([2, 'Bob'])
                ->fit(),
        ];
    }

    protected function wideTable(): TableContent
    {
        $table = TableContent::withHeaders([
            'ID', 'Product', 'Description', 'URL', 'Supplier reference', 'Warehouse', 'Stock', 'Updated at',
        ]);

        foreach (range(1, 5) as $id) {
            $table->row([
                $id,
                'Basketball shoe model '.$id,
                str_repeat('Long description which wraps in a fitted table instead of making it scroll. ', $id),
                'https://example.com/products/basketball-shoe-model-'.$id.'?utm_source=papers&utm_medium=table&utm_campaign=fit',
                'SUPPLIER-REFERENCE-WITHOUT-ANY-SPACES-'.str_repeat('X', 10 * $id),
                'Main warehouse',
                $id * 7,
                '2026-10-28 12:00:00',
            ]);
        }

        return $table;
    }
}
