<?php

namespace Schrojf\Papers\Contents;

use Illuminate\Contracts\Support\Renderable;
use Stringable;

class DataPanelContent extends Content implements Renderable
{
    protected string|Stringable|null $title = null;

    protected array $items = [];

    public static function make(): self
    {
        return new self;
    }

    public function title(string|Stringable|null $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function item(string|Stringable $label, string|Stringable $value): self
    {
        $this->items[] = compact('label', 'value');

        return $this;
    }

    public function render()
    {
        return view('papers::components.data-panel', [
            'title' => $this->title,
            'items' => $this->items,
        ])->render();
    }

    public function toHtml()
    {
        return $this->render();
    }
}
