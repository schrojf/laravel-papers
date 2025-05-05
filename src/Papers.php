<?php

namespace Schrojf\Papers;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use RuntimeException;

class Papers
{
    /**
     * Return CSS for the Papers app.
     */
    public function css(): string
    {
        $css = __DIR__.'/../dist/papers.css';

        if (($contents = @file_get_contents($css)) === false) {
            throw new RuntimeException("Unable to load Papers app CSS path [{$css}].");
        }

        return "<style>{$contents}</style>".PHP_EOL;
    }

    /**
     * Return the compiled JavaScript from the vendor directory.
     */
    public function js(): string
    {
        if (($js = @file_get_contents(__DIR__.'/../dist/papers.js')) === false) {
            throw new RuntimeException('Unable to load the Papers app JavaScript.');
        }

        $packageName = Js::from(static::scriptVariables());

        return new HtmlString(<<<HTML
            <script type="module">
                window.Papers = {$packageName};
                {$js}
            </script>
            HTML);
    }

    /**
     * Get the default JavaScript variables for Package.
     */
    public static function scriptVariables(): array
    {
        return [
            //
        ];
    }
}
