<?php

namespace Schrojf\Papers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Schrojf\Papers\Contents\ExceptionContent;
use Stringable;
use Throwable;

abstract class Paper
{
    /**
     * The description for the paper.
     *
     * @var Stringable|string|null
     */
    public static $description = null;

    /**
     * Indicates if the paper page should use the full width of the screen.
     *
     * @var bool
     */
    public static $fullWidth = false;

    /**
     * The additional words to find the paper by, besides its name and description.
     *
     * @var array<int, string>
     */
    public static $keywords = [];

    /**
     * Get the displayable name of the paper.
     *
     * @return Stringable|string
     */
    public static function name()
    {
        return Str::title(Str::snake(class_basename(get_called_class()), ' '));
    }

    /**
     * Get the description for the paper.
     *
     * @return Stringable|string|null
     */
    public static function description()
    {
        return static::$description;
    }

    /**
     * Determine if the paper page should use the full width of the screen.
     */
    public static function fullWidth(): bool
    {
        return static::$fullWidth;
    }

    /**
     * Get the additional words to find the paper by, besides its name and description.
     *
     * @return array<int, string>
     */
    public static function keywords(): array
    {
        return static::$keywords;
    }

    /**
     * Get the plain text data the papers filter searches in.
     *
     * @return array{name: string, description: string, handler: string, keywords: array<int, string>}
     */
    public static function searchData(): array
    {
        return [
            'name' => strip_tags((string) static::name()),
            'description' => strip_tags((string) static::description()),
            'handler' => static::handler(),
            'keywords' => array_map(fn ($keyword) => strip_tags((string) $keyword), static::keywords()),
        ];
    }

    /**
     * Get the URI handler for the paper.
     *
     * @return string
     */
    public static function handler()
    {
        return Str::kebab(class_basename(static::class));
    }

    /**
     * Get the URL of the paper page, or of its page with the given name.
     *
     * @param  array<string, mixed>  $query
     */
    public static function url(?string $page = null, array $query = []): string
    {
        return self::urlTo('papers.show', 'page', $page, $query);
    }

    /**
     * Get the URL of the paper's action, or of its action with the given name.
     *
     * @param  array<string, mixed>  $query
     */
    public static function actionUrl(?string $action = null, array $query = []): string
    {
        return self::urlTo('papers.action', 'action', $action, $query);
    }

    /**
     * Get a redirect to the paper page, or to its page with the given name.
     *
     * @param  array<string, mixed>  $query
     */
    public static function redirect(?string $page = null, array $query = []): RedirectResponse
    {
        return redirect()->to(static::url($page, $query));
    }

    /**
     * Get the URL of the given paper route with an optional name.
     *
     * The query string is added separately, so a query parameter can never
     * take the place of the route parameter, e.g. "page" of a paginator.
     *
     * @param  array<string, mixed>  $query
     */
    private static function urlTo(string $route, string $parameter, ?string $name, array $query): string
    {
        $parameters = ['paper' => static::handler()];

        if (! is_null($name)) {
            $parameters[$parameter] = $name;
        }

        $url = route($route, $parameters);

        return empty($query) ? $url : $url.'?'.Arr::query($query);
    }

    /**
     * Get the sections displayed on the paper page.
     *
     * Papers with named pages, such as /papers/orders/done, accept the page
     * name by an optional parameter: sections(?string $page = null).
     */
    abstract public function sections(): array;

    /**
     * Resolve content from the sections of the paper page, or of its page with the given name.
     */
    public function resolveContent(?string $page = null): array
    {
        $content = [];
        $hasError = false;
        $notExecuted = [];

        // The page name is passed only when there is one, so sections() may have a default page.
        // It is passed to the optional parameter papers with named pages add to sections().
        /** @phpstan-ignore-next-line */
        $sections = is_null($page) ? $this->sections() : $this->sections($page);

        foreach ($sections as $name => $section) {
            if ($hasError) {
                $notExecuted[] = $name;

                continue;
            }

            if (is_callable($section)) {
                try {
                    $content[] = [
                        'type' => 'section',
                        'name' => $name,
                        'content' => Arr::wrap(call_user_func($section)),
                    ];
                } catch (Throwable $e) {
                    $hasError = true;
                    $content[] = [
                        'type' => 'section',
                        'name' => $name,
                        'content' => [
                            new ExceptionContent($e),
                        ],
                    ];
                }
            } else {
                $content[] = [
                    'type' => $name,
                ];
            }
        }

        if (! empty($notExecuted)) {
            $content[] = [
                'type' => 'error',
                'content' => $notExecuted,
            ];
        }

        return $content;
    }
}
