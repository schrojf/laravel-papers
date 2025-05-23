<?php

namespace Schrojf\Papers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class Paper
{
    /**
     * The description for the paper.
     *
     * @var \Stringable|string|null
     */
    public static $description = null;

    /**
     * Get the displayable name of the paper.
     *
     * @return \Stringable|string
     */
    public static function name()
    {
        return Str::title(Str::snake(class_basename(get_called_class()), ' '));
    }

    /**
     * Get the description for the paper.
     *
     * @return \Stringable|string|null
     */
    public static function description()
    {
        return static::$description;
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
     *
     * @return array
     */
    abstract public function sections(): array;

    /**
     * Resolve content from defined sections.
     *
     * @return array
     */
    public function resolveContent(): array
    {
        $content = [];

        foreach ($this->sections() as $name => $section) {
            if (is_callable($section)) {
                $content[] = [
                    'type' => 'section',
                    'name' => $name,
                    'content' => Arr::wrap(call_user_func($section)),
                ];
            } else {
                $content[] = [
                    'type' => $name,
                ];
            }
        }

        return $content;
    }
}
