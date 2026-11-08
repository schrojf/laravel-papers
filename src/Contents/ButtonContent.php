<?php

namespace Schrojf\Papers\Contents;

use Illuminate\Contracts\Support\Renderable;
use InvalidArgumentException;
use Stringable;

/**
 * A button submitting a POST request to an action, such as a paper's actionUrl().
 * It is a form of its own with a CSRF token, so it can be placed anywhere,
 * e.g. into table rows.
 */
class ButtonContent extends Content implements Renderable
{
    /**
     * The styles of buttons.
     */
    public const STYLES = ['primary', 'secondary', 'danger'];

    protected string|Stringable $label;

    protected string $action;

    /**
     * @var array<string, mixed>
     */
    protected array $hidden = [];

    protected ?string $confirm = null;

    protected string $style = 'secondary';

    public function __construct(string|Stringable $label, string $action)
    {
        $this->label = $label;
        $this->action = $action;
    }

    public static function make(string|Stringable $label, string $action): self
    {
        return new self($label, $action);
    }

    /**
     * Send the given value with the request.
     */
    public function hidden(string $name, mixed $value): self
    {
        $this->hidden[$name] = $value;

        return $this;
    }

    /**
     * Ask the user to confirm the action with the given message first.
     */
    public function confirm(?string $message): self
    {
        $this->confirm = $message;

        return $this;
    }

    /**
     * Set the style of the button: primary, secondary or danger.
     */
    public function style(string $style): self
    {
        $this->style = static::validStyle($style);

        return $this;
    }

    public function primary(): self
    {
        return $this->style('primary');
    }

    public function danger(): self
    {
        return $this->style('danger');
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function validStyle(string $style): string
    {
        if (! in_array($style, self::STYLES, true)) {
            throw new InvalidArgumentException("Button style [{$style}] is not supported. Use one of: ".implode(', ', self::STYLES).'.');
        }

        return $style;
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'action' => $this->action,
            'hidden' => $this->hidden,
            'confirm' => $this->confirm,
            'style' => $this->style,
        ];
    }

    public function render()
    {
        return view('papers::components.button', $this->toArray())->render();
    }

    public function toHtml()
    {
        return $this->render();
    }
}
