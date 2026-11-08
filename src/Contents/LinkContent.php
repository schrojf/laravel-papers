<?php

namespace Schrojf\Papers\Contents;

use Illuminate\Contracts\Support\Renderable;
use Stringable;

/**
 * A link, such as to a paper page or one of its named pages by url().
 * It may look like a button, e.g. for the next step after an action.
 */
class LinkContent extends Content implements Renderable
{
    protected string|Stringable $label;

    protected string $url;

    protected ?string $style = null;

    public function __construct(string|Stringable $label, string $url)
    {
        $this->label = $label;
        $this->url = $url;
    }

    public static function make(string|Stringable $label, string $url): self
    {
        return new self($label, $url);
    }

    /**
     * Make the link look like a button of the given style: primary, secondary or danger.
     */
    public function button(string $style = 'secondary'): self
    {
        $this->style = ButtonContent::validStyle($style);

        return $this;
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'url' => $this->url,
            'style' => $this->style,
        ];
    }

    public function render()
    {
        return view('papers::components.link', $this->toArray())->render();
    }

    public function toHtml()
    {
        return $this->render();
    }
}
