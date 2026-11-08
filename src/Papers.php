<?php

namespace Schrojf\Papers;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Foundation\Vite;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use RuntimeException;
use Schrojf\Papers\Traits\SponsorSupportable;

final class Papers
{
    use SponsorSupportable;

    /**
     * The directory the assets are published to, relative to the public path.
     */
    public const BUILD_DIRECTORY = 'vendor/papers';

    /**
     * The path to the "hot" file written while the Vite development server is running.
     */
    public static string $hotFile = __DIR__.'/../vite.hot';

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
     * Get the CSS for the Papers app.
     */
    public static function css(): Htmlable
    {
        if (self::shouldInlineAssets()) {
            $css = self::distContents('resources/css/papers.css');

            $nonce = self::nonceAttribute();

            return new HtmlString(<<<HTML
                <style{$nonce}>{$css}</style>
                HTML);
        }

        return self::vite('resources/css/papers.css');
    }

    /**
     * Get the JavaScript for the Papers app.
     */
    public static function js(): Htmlable
    {
        // Cast to an object, so empty variables are a JavaScript object rather than an array
        $variables = Js::from((object) self::scriptVariables());

        $nonce = self::nonceAttribute();

        if (self::shouldInlineAssets()) {
            $js = self::distContents('resources/js/papers.js');

            return new HtmlString(<<<HTML
                <script type="module"{$nonce}>
                    window.Papers = {$variables};
                    {$js}
                </script>
                HTML);
        }

        $script = self::vite('resources/js/papers.js');

        return new HtmlString(<<<HTML
            <script{$nonce}>window.Papers = {$variables};</script>
            {$script}
            HTML);
    }

    /**
     * Get the default JavaScript variables for the Papers app.
     */
    public static function scriptVariables(): array
    {
        return [
            //
        ];
    }

    /**
     * Determine if the published assets are up-to-date with the package's compiled assets.
     */
    public static function assetsAreCurrent(): bool
    {
        $publishedManifest = public_path(self::BUILD_DIRECTORY.'/manifest.json');

        return is_file($publishedManifest)
            && file_get_contents($publishedManifest) === self::distFile('manifest.json');
    }

    /**
     * Determine if the assets should be inlined, rather than loaded from the Vite development server or the published assets.
     *
     * Inlined assets can't load chunks or reference other assets, such as fonts or images.
     */
    protected static function shouldInlineAssets(): bool
    {
        return ! is_file(self::$hotFile) && ! self::assetsAreCurrent();
    }

    /**
     * Get the Vite tags for the given entrypoint.
     */
    protected static function vite(string $entrypoint): HtmlString
    {
        // Clone the application's Vite instance, so its configuration is kept but not changed
        $vite = (clone app(Vite::class))->useHotFile(self::$hotFile);

        return $vite($entrypoint, self::BUILD_DIRECTORY);
    }

    /**
     * Get the contents of the compiled asset for the given entrypoint.
     */
    protected static function distContents(string $entrypoint): string
    {
        $manifest = json_decode(self::distFile('manifest.json'), true);

        if (! isset($manifest[$entrypoint]['file'])) {
            throw new RuntimeException("Unable to locate the Papers asset [{$entrypoint}] in the manifest.");
        }

        return self::distFile($manifest[$entrypoint]['file']);
    }

    /**
     * Get the contents of the given compiled file.
     */
    protected static function distFile(string $file): string
    {
        $path = __DIR__.'/../dist/'.$file;

        if (($contents = @file_get_contents($path)) === false) {
            throw new RuntimeException("Unable to load the Papers asset [{$path}].");
        }

        return $contents;
    }

    /**
     * Get the nonce attribute for inline tags, using the application's Vite CSP nonce.
     */
    protected static function nonceAttribute(): string
    {
        if (($nonce = app(Vite::class)->cspNonce()) === null) {
            return '';
        }

        return ' nonce="'.e($nonce).'"';
    }
}
