<?php

namespace Tests\fixtures\Papers;

use Illuminate\Support\HtmlString;
use Schrojf\Papers\Paper;

class SearchableTestPaper extends Paper
{
    public static $keywords = ['tržby', '<b>revenue</b>'];

    public static function name()
    {
        return 'Predaj "podľa" značiek & kolekcií';
    }

    public static function description()
    {
        return new HtmlString('<strong>Sales</strong> by brand');
    }

    public function sections(): array
    {
        return [];
    }
}
