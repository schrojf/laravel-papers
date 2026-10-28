<?php

namespace Schrojf\Papers;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
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
     * @var array<int, class-string<Paper>>
     */
    public static array $papers = [];

    /**
     * The callable that handler the paper was not found.
     *
     * @var callable(Request): void|null
     */
    protected static $paperNotFoundCallback = null;

    /**
     * The URL of the favicon of the papers pages.
     */
    protected static ?string $favicon = null;

    /**
     * The HTML added to the head of the papers pages.
     *
     * @var Closure(): (Htmlable|string|null)|Htmlable|string|null
     */
    protected static $head = null;

    /**
     * Register the given papers.
     *
     * @param  array<int, class-string<Paper>>  $papers
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
     * @return array<int, class-string<Paper>>
     */
    public static function all(): array
    {
        return self::$papers;
    }

    /**
     * Replace the registered papers with the given papers.
     *
     * @param  array<int, class-string<Paper>>  $papers
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
     * @param  callable(Request): void|null  $paperNotFoundCallback
     */
    public static function handlePaperNotFound(?callable $paperNotFoundCallback): self
    {
        self::$paperNotFoundCallback = $paperNotFoundCallback;

        return new self;
    }

    /**
     * Set the URL of the favicon of the papers pages.
     */
    public static function favicon(?string $url): self
    {
        self::$favicon = $url;

        return new self;
    }

    /**
     * Set the HTML added to the head of the papers pages, such as icons or a web manifest.
     * It is not escaped. A closure is called whenever a page is rendered.
     *
     * @param  Closure(): (Htmlable|string|null)|Htmlable|string|null  $head
     */
    public static function head(Closure|Htmlable|string|null $head): self
    {
        self::$head = $head;

        return new self;
    }

    /**
     * Render the favicon and the HTML added to the head of the papers pages.
     */
    public static function renderHead(): HtmlString
    {
        $html = '';

        if (self::$favicon !== null) {
            $html .= '<link rel="icon" href="'.e(self::$favicon).'">'.PHP_EOL;
        }

        $head = self::$head instanceof Closure ? call_user_func(self::$head) : self::$head;

        if ($head instanceof Htmlable) {
            $head = $head->toHtml();
        }

        return new HtmlString($html.$head);
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
