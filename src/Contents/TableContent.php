<?php

namespace Schrojf\Papers\Contents;

use Illuminate\Contracts\Support\Renderable;
use InvalidArgumentException;

class TableContent extends Content implements Renderable
{
    protected array $headers;

    protected array $rows = [];

    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    public static function withHeaders(array $headers): self
    {
        return new self($headers);
    }

    public function row(array $row): self
    {
        if (count($row) > count($this->headers)) {
            throw new InvalidArgumentException('Row has more cells than headers.');
        }

        $this->rows[] = $row;

        return $this;
    }

    public function rows(array $rows): self
    {
        foreach ($rows as $row) {
            $this->row($row);
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'headers' => $this->headers,
            'rows' => $this->rows,
        ];
    }

    public function render()
    {
        return view('papers::components.table', $this->toArray())->render();
    }

    public function toHtml()
    {
        return $this->render();
    }
}
