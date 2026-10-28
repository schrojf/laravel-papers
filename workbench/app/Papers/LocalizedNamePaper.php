<?php

namespace App\Papers;

use Schrojf\Papers\Paper;

class LocalizedNamePaper extends Paper
{
    public static $description = 'Paper with a name in Slovak. The filter ignores diacritics, so "stitky" finds it.';

    public static function name()
    {
        return 'Štítky podľa pozície';
    }

    public function sections(): array
    {
        return [
            'Intro' => fn () => 'Type "stitky pozic" into the filter to find this paper.',
        ];
    }
}
