<?php

namespace Schrojf\Papers;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use RuntimeException;

class Papers
{
    /**
     * The registered paper classes.
     *
     * @var array<int, class-string<\Schrojf\Papers\Paper>>
     */
    public static array $papers = [];

    /**
     * Register the given papers.
     *
     * @param  array<int, class-string<\Schrojf\Papers\Paper>>  $papers
     */
    public static function register(array $papers): static
    {
        static::$papers = array_unique(
            array_merge(static::$papers, $papers)
        );

        return new static;
    }

    /**
     *  Get all the available papers.
     *
     * @return array<int, class-string<\Schrojf\Papers\Paper>>
     */
    public static function all(): array
    {
        return static::$papers;
    }

    /**
     * Replace the registered papers with the given papers.
     *
     * @param  array<int, class-string<\Schrojf\Papers\Paper>>  $papers
     */
    public static function replacePapers(array $papers): static
    {
        static::$papers = $papers;

        return new static;
    }

    public static function paperForHandler(string|null $handler)
    {
        foreach (static::$papers as $paper) {
            if ($paper::handler() === $handler) {
                return $paper;
            }
        }

        return null;
    }

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
