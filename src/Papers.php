<?php

namespace Schrojf\Papers;

use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use RuntimeException;
use Schrojf\Papers\Traits\SponsorSupportable;

final class Papers
{
    use SponsorSupportable;

    /**
     * The registered paper classes.
     *
     * @var array<int, class-string<\Schrojf\Papers\Paper>>
     */
    public static array $papers = [];

    /**
     * The callable that handler the paper was not found.
     *
     * @var callable(\Illuminate\Http\Request): void|null
     */
    protected static $paperNotFoundCallback = null;

    /**
     * Register the given papers.
     *
     * @param  array<int, class-string<\Schrojf\Papers\Paper>>  $papers
     */
    public static function register(array $papers): self
    {
        self::$papers = array_unique(
            array_merge(self::$papers, $papers)
        );

        return new self;
    }

    /**
     *  Get all the available papers.
     *
     * @return array<int, class-string<\Schrojf\Papers\Paper>>
     */
    public static function all(): array
    {
        return self::$papers;
    }

    /**
     * Replace the registered papers with the given papers.
     *
     * @param  array<int, class-string<\Schrojf\Papers\Paper>>  $papers
     */
    public static function replacePapers(array $papers): self
    {
        self::$papers = $papers;

        return new self;
    }

    public static function paperForHandler(?string $handler)
    {
        foreach (self::$papers as $paper) {
            if ($paper::handler() === $handler) {
                return $paper;
            }
        }

        return null;
    }

    public static function paperNotFound(Request $request): never
    {
        if (self::$paperNotFoundCallback) {
            call_user_func(self::$paperNotFoundCallback, $request);
        }

        abort(404, 'Paper not found');
    }

    /**
     * Set the callable that resolves the user's preferred locale.
     *
     * @param  callable(\Illuminate\Http\Request): void|null  $paperNotFoundCallback
     */
    public static function handlePaperNotFound(?callable $paperNotFoundCallback): self
    {
        self::$paperNotFoundCallback = $paperNotFoundCallback;

        return new self;
    }

    /**
     * Return CSS for the Papers app.
     */
    public static function css(): string
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
    public static function js(): string
    {
        if (($js = @file_get_contents(__DIR__.'/../dist/papers.js')) === false) {
            throw new RuntimeException('Unable to load the Papers app JavaScript.');
        }

        $packageName = Js::from(self::scriptVariables());

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
