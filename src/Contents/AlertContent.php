<?php

namespace Schrojf\Papers\Contents;

use Illuminate\Contracts\Support\Renderable;
use InvalidArgumentException;
use Stringable;

/**
 * A highlighted message, such as the success or the failure of an action.
 */
class AlertContent extends Content implements Renderable
{
    /**
     * The types of alerts.
     */
    public const TYPES = ['success', 'error', 'warning', 'info'];

    protected string $type;

    protected string|Stringable $message;

    protected string|Stringable|null $title = null;

    public function __construct(string $type, string|Stringable $message)
    {
        if (! in_array($type, self::TYPES, true)) {
            throw new InvalidArgumentException("Alert type [{$type}] is not supported. Use one of: ".implode(', ', self::TYPES).'.');
        }

        $this->type = $type;
        $this->message = $message;
    }

    public static function make(string $type, string|Stringable $message): self
    {
        return new self($type, $message);
    }

    public static function success(string|Stringable $message): self
    {
        return new self('success', $message);
    }

    public static function error(string|Stringable $message): self
    {
        return new self('error', $message);
    }

    public static function warning(string|Stringable $message): self
    {
        return new self('warning', $message);
    }

    public static function info(string|Stringable $message): self
    {
        return new self('info', $message);
    }

    public function title(string|Stringable|null $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
            'title' => $this->title,
        ];
    }

    public function render()
    {
        return view('papers::components.alert', $this->toArray())->render();
    }

    public function toHtml()
    {
        return $this->render();
    }
}
