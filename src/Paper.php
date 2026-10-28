<?php

namespace Schrojf\Papers;

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
     * Get the sections displayed on the paper page.
     */
    abstract public function sections(): array;

    /**
     * Resolve content from defined sections.
     */
    public function resolveContent(): array
    {
        $content = [];
        $hasError = false;
        $notExecuted = [];

        foreach ($this->sections() as $name => $section) {
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
